import { 
    db, doc, getDoc, collection, getDocs, addDoc, updateDoc, deleteDoc,
    auth, onAuthStateChanged 
} from './firebase.js';

// ===== ИНИЦИАЛИЗАЦИЯ =====

// 1. Сайтын тохиргоо (Settings)
async function initSite() {
    try {
        const docSnap = await getDoc(doc(db, "siteSettings", "main"));
        if (docSnap.exists()) {
            const d = docSnap.data();
            if (d.heroTitle) document.getElementById('hero-title').innerText = d.heroTitle;
            if (d.heroSubtitle) document.getElementById('hero-subtitle').innerText = d.heroSubtitle;
            if (d.aboutDescription) document.getElementById('about-description').innerText = d.aboutDescription;
            if (d.cvUrl) document.getElementById('cv-link').href = d.cvUrl;
            if (d.footerText) document.getElementById('footer-text').innerText = d.footerText;
        }
    } catch (error) {
        console.error('Сайтын тохиргоо ачаалахад алдаа:', error);
    }
}

// 2. Ур чадварууд харуулах
async function displaySkills() {
    try {
        const snap = await getDocs(collection(db, "skills"));
        const container = document.getElementById('skills-container');
        container.innerHTML = '';
        
        if (snap.empty) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999;">Ур чадварууд ачаалагдаагүй байна</p>';
            return;
        }

        snap.forEach(doc => {
            const s = doc.data();
            container.innerHTML += `
                <div class="card skill-card" data-id="${doc.id}">
                    <div class="icon">${s.icon || '⭐'}</div>
                    <h3>${s.name || 'Skill'}</h3>
                    <span class="badge">${s.level || 'Unknown'}</span>
                    <p style="font-size: 0.85rem; color: #666; margin-top: 10px;">${s.description || ''}</p>
                </div>`;
        });
    } catch (error) {
        console.error('Ур чадварууд ачаалахад алдаа:', error);
    }
}

// 3. Төслүүд харуулах
async function displayProjects() {
    try {
        const snap = await getDocs(collection(db, "projects"));
        const container = document.getElementById('projects-container');
        container.innerHTML = '';
        
        if (snap.empty) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999;">Төслүүд ачаалагдаагүй байна</p>';
            return;
        }

        snap.forEach(doc => {
            const p = doc.data();
            const imgUrl = p.imageUrl || 'https://via.placeholder.com/320x200?text=Project';
            container.innerHTML += `
                <div class="card project-card" data-id="${doc.id}">
                    <img src="${imgUrl}" alt="${p.title || 'Project'}" 
                         onerror="this.src='https://via.placeholder.com/320x200?text=Error'">
                    <div class="p-content">
                        <h3>${p.title || 'Төсөл'}</h3>
                        <p>${p.shortDescription || 'Тайлбар байхгүй'}</p>
                        <small>
                            <strong>Технологи:</strong> ${p.techStack || 'Unknown'}
                        </small>
                        ${p.githubUrl ? `<a href="${p.githubUrl}" target="_blank">
                            <i class="fab fa-github"></i> Кодыг үзэх
                        </a>` : ''}
                        ${p.liveUrl ? `<a href="${p.liveUrl}" target="_blank" style="margin-left: 10px;">
                            <i class="fas fa-external-link-alt"></i> Live Demo
                        </a>` : ''}
                    </div>
                </div>`;
        });
    } catch (error) {
        console.error('Төслүүд ачаалахад алдаа:', error);
    }
}

// 4. Хичээлүүд харуулах
async function displayCourses() {
    try {
        const snap = await getDocs(collection(db, "courses"));
        const container = document.getElementById('courses-container');
        container.innerHTML = '';
        
        if (snap.empty) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999;">Хичээлүүд ачаалагдаагүй байна</p>';
            return;
        }

        snap.forEach(doc => {
            const c = doc.data();
            const status = c.status || 'Явалцаалж байна';
            const statusColor = status === 'Дууссан' ? '#10b981' : '#f59e0b';
            
            container.innerHTML += `
                <div class="course-item" data-id="${doc.id}">
                    <h3>${c.name || 'Хичээл'}</h3>
                    <p style="color: #666; margin: 5px 0; font-size: 0.9rem;">
                        <strong>Семестр:</strong> ${c.semester || 'Unknown'}
                    </p>
                    <p style="color: #666; margin: 5px 0; font-size: 0.9rem;">
                        <strong>Баллын оноо:</strong> ${c.credits || 0}
                    </p>
                    <span style="display: inline-block; background-color: ${statusColor}; color: white; 
                                 padding: 4px 10px; border-radius: 5px; font-size: 0.8rem; margin-top: 10px;">
                        ${status}
                    </span>
                </div>`;
        });
    } catch (error) {
        console.error('Хичээлүүд ачаалахад алдаа:', error);
    }
}

// ===== ADMIN ХОЛБООС ХАРУУЛАХ =====
onAuthStateChanged(auth, (user) => {
    const adminLink = document.querySelector('.admin-link');
    if (user) {
        if (adminLink) adminLink.style.display = 'block';
    } else {
        if (adminLink) adminLink.style.display = 'block';
    }
});

// ===== ДУУДЛАГУУД =====
document.addEventListener('DOMContentLoaded', () => {
    initSite();
    displaySkills();
    displayProjects();
    displayCourses();
    
    // Smooth scroll эффект
    setupSmoothScroll();
    
    // Mobile menu
    setupMobileMenu();
});

// Smooth Scroll
function setupSmoothScroll() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#' && document.querySelector(href)) {
                // Mobile menu хаах
                const hamburger = document.getElementById('hamburger');
                const navLinks = document.querySelector('.nav-links');
                if (navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    hamburger.classList.remove('active');
                }
            }
        });
    });
}

// Mobile Menu
function setupMobileMenu() {
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.querySelector('.nav-links');

    if (!hamburger) return;

    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
    });

    // Товч дээр дарахад цэс хаах
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
            hamburger.classList.remove('active');
        });
    });
}

// Export for admin
export { displaySkills, displayProjects, displayCourses };
