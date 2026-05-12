import {
    db, collection, getDocs, doc, getDoc, addDoc, updateDoc, deleteDoc,
    auth, signInWithEmailAndPassword, onAuthStateChanged, signOut
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
                    <img src="${skill.imageUrl || 'https://via.placeholder.com/50'}" alt="${skill.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 10px; vertical-align: middle;">
                    <h4 style="display: inline-block; vertical-align: middle;">${skill.name}</h4>
                    <p style="margin-top: 5px;">Түвшин: <strong>${skill.level}</strong></p>
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
                imageUrl: document.getElementById('skill-image').value,
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
        const newImage = prompt('Шинэ зураг (URL):', skill.imageUrl);
        const newLevel = prompt('Шинэ түвшин (Beginner/Intermediate/Advanced):', skill.level);

        if (newName && newImage && newLevel) {
            try {
                await updateDoc(doc(db, 'skills', id), {
                    name: newName,
                    imageUrl: newImage,
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
            await addDoc(collection(db, 'projects'), {
                title: document.getElementById('project-title').value,
                shortDescription: document.getElementById('project-desc').value,
                imageUrl: document.getElementById('project-image').value,
                techStack: document.getElementById('project-tech').value,
                githubUrl: document.getElementById('project-github').value,
                createdAt: new Date()
            });

            projectForm.reset();
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
        const newImage = prompt('Шинэ зураг (URL):', proj.imageUrl);

        if (newTitle && newImage) {
            try {
                await updateDoc(doc(db, 'projects', id), {
                    title: newTitle,
                    imageUrl: newImage,
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
                    <img src="${course.imageUrl || 'https://via.placeholder.com/50'}" alt="${course.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 10px; vertical-align: middle;">
                    <h4 style="display: inline-block; vertical-align: middle;">${course.name}</h4>
                    <p style="margin-top: 5px;"><strong>Семестр:</strong> ${course.semester}</p>
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
                imageUrl: document.getElementById('course-image').value,
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
        const newImage = prompt('Шинэ зураг (URL):', course.imageUrl);

        if (newName && newSemester && newImage) {
            try {
                await updateDoc(doc(db, 'courses', id), {
                    name: newName,
                    semester: newSemester,
                    imageUrl: newImage,
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
