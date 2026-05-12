import { 
    db, collection, getDocs, doc, getDoc, addDoc, updateDoc, deleteDoc,
    auth, signInWithEmailAndPassword, onAuthStateChanged, signOut,  storage, ref, uploadBytes, getDownloadURL
} from '../js/firebase.js';

// ============ НЭВТРЭЛТ УДИРДАХ ============
const loginForm = document.getElementById('form-login');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const loginError = document.getElementById('login-error');
const logoutBtn = document.getElementById('logout-btn');

if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await signInWithEmailAndPassword(auth, emailInput.value, passwordInput.value);
            loginError.textContent = '';
            loginForm.reset();
        } catch (error) {
            loginError.textContent = '❌ ' + error.message;
        }
    });
}

if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        signOut(auth).catch(err => console.error(err));
    });
}

// ============ НЭВТРЭХ СТАТУС ШАЛГАХ ============
onAuthStateChanged(auth, (user) => {
    const loginFormDiv = document.getElementById('login-form');
    const adminContent = document.getElementById('admin-content');
    
    if (user) {
        if (loginFormDiv) loginFormDiv.style.display = 'none';
        if (adminContent) adminContent.style.display = 'block';
        loadAllData();
    } else {
        if (loginFormDiv) loginFormDiv.style.display = 'flex';
        if (adminContent) adminContent.style.display = 'none';
    }
});

// ============ ТАБ СОНГОЛТ ============
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        
        btn.classList.add('active');
        const tabName = btn.getAttribute('data-tab');
        document.getElementById(tabName).classList.add('active');
    });
});

// ============ 1. САЙТЫН ТОХИРГОО ============
const settingsForm = document.getElementById('form-settings');

async function loadSettings() {
    try {
        const docSnap = await getDoc(doc(db, 'siteSettings', 'main'));
        if (docSnap.exists()) {
            const data = docSnap.data();
            document.getElementById('heroTitle').value = data.heroTitle || '';
            document.getElementById('heroSubtitle').value = data.heroSubtitle || '';
            document.getElementById('cvUrl').value = data.cvUrl || '';
            document.getElementById('footerText').value = data.footerText || '';
        }
    } catch (error) {
        console.error('Тохиргоо ачаалахад алдаа:', error);
    }
}

if (settingsForm) {
    settingsForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await updateDoc(doc(db, 'siteSettings', 'main'), {
                heroTitle: document.getElementById('heroTitle').value,
                heroSubtitle: document.getElementById('heroSubtitle').value,
                cvUrl: document.getElementById('cvUrl').value,
                footerText: document.getElementById('footerText').value,
                updatedAt: new Date()
            });
            alert('✅ Сайтын тохиргоо амжилттай шинэчлэгдлээ!');
        } catch (error) {
            alert('❌ Алдаа: ' + error.message);
        }
    });
}

// ============ 2. УР ЧАДВАРУУД ============
const skillForm = document.getElementById('form-add-skill');
const skillsList = document.getElementById('skills-list');

async function loadSkills() {
    try {
        skillsList.innerHTML = '';
        const snap = await getDocs(collection(db, 'skills'));
        snap.forEach(doc => {
            const skill = doc.data();
            const skillCard = document.createElement('div');
            skillCard.className = 'item-card';
            skillCard.innerHTML = `
                <div class="item-info">
                    <h4>${skill.icon} ${skill.name}</h4>
                    <p>Түвшин: <strong>${skill.level}</strong></p>
                </div>
                <div class="item-actions">
                    <button class="btn-edit" onclick="editSkill('${doc.id}')">✏️ Засах</button>
                    <button class="btn-delete" onclick="deleteSkill('${doc.id}')">🗑️ Хасах</button>
                </div>
            `;
            skillsList.appendChild(skillCard);
        });
    } catch (error) {
        console.error('Ур чадварууд ачаалахад алдаа:', error);
    }
}

if (skillForm) {
    skillForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await addDoc(collection(db, 'skills'), {
                name: document.getElementById('skill-name').value,
                icon: document.getElementById('skill-icon').value,
                level: document.getElementById('skill-level').value,
                createdAt: new Date()
            });
            skillForm.reset();
            loadSkills();
            alert('✅ Ур чадвар амжилттай нэмэгдлээ!');
        } catch (error) {
            alert('❌ Алдаа: ' + error.message);
        }
    });
}

window.deleteSkill = async (id) => {
    if (confirm('Энэ ур чадварыг устгахдаа итгэлтэй байна уу?')) {
        try {
            await deleteDoc(doc(db, 'skills', id));
            loadSkills();
            alert('✅ Ур чадвар амжилттай устгагдлээ!');
        } catch (error) {
            alert('❌ Алдаа: ' + error.message);
        }
    }
};

window.editSkill = async (id) => {
    const skillSnap = await getDoc(doc(db, 'skills', id));
    if (skillSnap.exists()) {
        const skill = skillSnap.data();
        const newName = prompt('Шинэ нэр:', skill.name);
        const newIcon = prompt('Шинэ иконка:', skill.icon);
        const newLevel = prompt('Шинэ түвшин (Beginner/Intermediate/Advanced):', skill.level);
        
        if (newName && newIcon && newLevel) {
            try {
                await updateDoc(doc(db, 'skills', id), {
                    name: newName,
                    icon: newIcon,
                    level: newLevel,
                    updatedAt: new Date()
                });
                loadSkills();
                alert('✅ Ур чадвар амжилттай засагдлээ!');
            } catch (error) {
                alert('❌ Алдаа: ' + error.message);
            }
        }
    }
};

// ============ 3. ТӨСЛҮҮД ============
const projectForm = document.getElementById('form-add-project');
const projectsList = document.getElementById('projects-list');
const projectImageFile = document.getElementById('project-image-file');
const projectImageBase64 = document.getElementById('project-image-base64');
const projectImagePreview = document.getElementById('project-image-preview');

// Зураг шахах функц (Firestore 1MB хязгаарыг давахгүйн тулд)
function compressImage(file, maxWidth = 800, quality = 0.7) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = event => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                resolve(canvas.toDataURL('image/jpeg', quality));
            };
            img.onerror = error => reject(error);
        };
        reader.onerror = error => reject(error);
    });
}

if (projectImageFile) {
    projectImageFile.addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (file) {
            try {
                // Preview-д зориулж шахаж харуулах
                const base64 = await compressImage(file, 400, 0.6);
                projectImageBase64.value = base64;
                projectImagePreview.src = base64;
                projectImagePreview.style.display = 'block';
            } catch (err) {
                console.error("Зураг уншихад алдаа гарлаа", err);
            }
        } else {
            projectImageBase64.value = '';
            projectImagePreview.src = '';
            projectImagePreview.style.display = 'none';
        }
    });
}

async function loadProjects() {
    try {
        projectsList.innerHTML = '';
        const snap = await getDocs(collection(db, 'projects'));
        snap.forEach(doc => {
            const project = doc.data();
            const projectCard = document.createElement('div');
            projectCard.className = 'item-card';
            projectCard.innerHTML = `
                <div class="item-info">
                    <h4>🚀 ${project.title}</h4>
                    <p>${project.shortDescription}</p>
                    <p><strong>Технологи:</strong> ${project.techStack}</p>
                    <p><a href="${project.githubUrl}" target="_blank">GitHub →</a></p>
                </div>
                <div class="item-actions">
                    <button class="btn-edit" onclick="editProject('${doc.id}')">✏️ Засах</button>
                    <button class="btn-delete" onclick="deleteProject('${doc.id}')">🗑️ Хасах</button>
                </div>
            `;
            projectsList.appendChild(projectCard);
        });
    } catch (error) {
        console.error('Төслүүд ачаалахад алдаа:', error);
    }
}

if (projectForm) {
    projectForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitBtn = projectForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = '⏳ Түр хүлээнэ үү...';

        try {
            const file = projectImageFile.files[0];
            let imageUrl = '';

            if (file) {
                try {
                    // 1. Firebase Storage рүү хуулахыг оролдох
                    const imageRef = ref(storage, `projects/${Date.now()}-${file.name}`);
                    await uploadBytes(imageRef, file);
                    imageUrl = await getDownloadURL(imageRef);
                } catch (storageError) {
                    console.warn("Storage upload амжилтгүй, Base64 рүү хөрвүүлж байна...", storageError);
                    // 2. Хэрэв Storage тохиргоогүй/алдаа заавал шахагдсан Base64 ашиглах (Firestore хязгаарт багтаана)
                    imageUrl = await compressImage(file, 800, 0.7);
                }
            } else {
                // Зураг сонгоогүй бол
                imageUrl = projectImageBase64.value || '';
            }

            // 3. Firestore руу хадгална
            await addDoc(collection(db, 'projects'), {
                title: document.getElementById('project-title').value,
                shortDescription: document.getElementById('project-desc').value,
                imageUrl: imageUrl,
                techStack: document.getElementById('project-tech').value,
                githubUrl: document.getElementById('project-github').value,
                createdAt: new Date()
            });

            projectForm.reset();

            if (projectImagePreview) {
                projectImagePreview.src = '';
                projectImagePreview.style.display = 'none';
            }
            if (projectImageBase64) {
                projectImageBase64.value = '';
            }

            loadProjects();
            alert('✅ Төсөл амжилттай нэмэгдлээ!');

        } catch (error) {
            console.error(error);
            alert('❌ Алдаа: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

window.deleteProject = async (id) => {
    if (confirm('Энэ төслийг устгахдаа итгэлтэй байна уу?')) {
        try {
            await deleteDoc(doc(db, 'projects', id));
            loadProjects();
            alert('✅ Төсөл амжилттай устгагдлээ!');
        } catch (error) {
            alert('❌ Алдаа: ' + error.message);
        }
    }
};

window.editProject = async (id) => {
    const projSnap = await getDoc(doc(db, 'projects', id));
    if (projSnap.exists()) {
        const proj = projSnap.data();
        const newTitle = prompt('Шинэ нэр:', proj.title);
        
        if (newTitle) {
            try {
                await updateDoc(doc(db, 'projects', id), {
                    title: newTitle,
                    updatedAt: new Date()
                });
                loadProjects();
                alert('✅ Төсөл амжилттай засагдлээ!');
            } catch (error) {
                alert('❌ Алдаа: ' + error.message);
            }
        }
    }
};

// ============ 4. ХИЧЭЭЛҮҮД ============
const courseForm = document.getElementById('form-add-course');
const coursesList = document.getElementById('courses-list');

async function loadCourses() {
    try {
        coursesList.innerHTML = '';
        const snap = await getDocs(collection(db, 'courses'));
        snap.forEach(doc => {
            const course = doc.data();
            const courseCard = document.createElement('div');
            courseCard.className = 'item-card';
            courseCard.innerHTML = `
                <div class="item-info">
                    <h4>📚 ${course.name}</h4>
                    <p><strong>Семестр:</strong> ${course.semester}</p>
                </div>
                <div class="item-actions">
                    <button class="btn-edit" onclick="editCourse('${doc.id}')">✏️ Засах</button>
                    <button class="btn-delete" onclick="deleteCourse('${doc.id}')">🗑️ Хасах</button>
                </div>
            `;
            coursesList.appendChild(courseCard);
        });
    } catch (error) {
        console.error('Хичээлүүд ачаалахад алдаа:', error);
    }
}

if (courseForm) {
    courseForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await addDoc(collection(db, 'courses'), {
                name: document.getElementById('course-name').value,
                semester: document.getElementById('course-semester').value,
                createdAt: new Date()
            });
            courseForm.reset();
            loadCourses();
            alert('✅ Хичээл амжилттай нэмэгдлээ!');
        } catch (error) {
            alert('❌ Алдаа: ' + error.message);
        }
    });
}

window.deleteCourse = async (id) => {
    if (confirm('Энэ хичээлийг устгахдаа итгэлтэй байна уу?')) {
        try {
            await deleteDoc(doc(db, 'courses', id));
            loadCourses();
            alert('✅ Хичээл амжилттай устгагдлаа!');
        } catch (error) {
            alert('❌ Алдаа: ' + error.message);
        }
    }
};

window.editCourse = async (id) => {
    const courseSnap = await getDoc(doc(db, 'courses', id));
    if (courseSnap.exists()) {
        const course = courseSnap.data();
        const newName = prompt('Шинэ нэр:', course.name);
        const newSemester = prompt('Шинэ семестр:', course.semester);
        
        if (newName && newSemester) {
            try {
                await updateDoc(doc(db, 'courses', id), {
                    name: newName,
                    semester: newSemester,
                    updatedAt: new Date()
                });
                loadCourses();
                alert('✅ Хичээл амжилттай засагдлээ!');
            } catch (error) {
                alert('❌ Алдаа: ' + error.message);
            }
        }
    }
};

// ============ БҮГДИЙГ АЧААЛАХ ============
async function loadAllData() {
    await loadSettings();
    await loadSkills();
    await loadProjects();
    await loadCourses();
}

// Эхэндээ ачаалах
loadAllData();
