import { db, doc, getDoc, collection, getDocs } from './firebase.js';

// 1. Сэтгэц (Settings)
async function initSite() {
    try {
        const docSnap = await getDoc(doc(db, "siteSettings", "main"));
        if (docSnap.exists()) {
            const d = docSnap.data();
            document.getElementById('hero-title').innerText = d.heroTitle || 'Сайн уу';
            document.getElementById('hero-subtitle').innerText = d.heroSubtitle || 'Вэб хөгжүүлэгч';
            document.getElementById('cv-link').href = d.cvUrl || '#';
            document.getElementById('footer-text').innerText = d.footerText || '© 2026 Portfolio';
        }
    } catch (error) {
        console.warn('Сайтын тохиргоо ачаалахад алдаа:', error);
    }
}

// 2. Skills харуулах
async function displaySkills() {
    try {
        const snap = await getDocs(collection(db, "skills"));
        const container = document.getElementById('skills-container');
        container.innerHTML = '';
        
        snap.forEach(doc => {
            const s = doc.data();
            container.innerHTML += `
                <div class="card skill-card">
                    <div class="icon">${s.icon}</div>
                    <h3>${s.name}</h3>
                    <span class="badge">${s.level}</span>
                </div>`;
        });
    } catch (error) {
        console.warn('Ур чадварууд ачаалахад алдаа:', error);
    }
}

// 3. Projects харуулах
async function displayProjects() {
    try {
        const snap = await getDocs(collection(db, "projects"));
        const container = document.getElementById('projects-container');
        container.innerHTML = '';
        
        snap.forEach(doc => {
            const p = doc.data();
            container.innerHTML += `
                <div class="card project-card">
                    <img src="${p.imageUrl}" alt="${p.title}" onerror="this.src='https://via.placeholder.com/300x150?text=${encodeURIComponent(p.title)}'">
                    <div class="p-content">
                        <h3>${p.title}</h3>
                        <p>${p.shortDescription}</p>
                        <small>Технологи: ${p.techStack}</small>
                        <a href="${p.githubUrl}" target="_blank">Линк <i class="fa-brands fa-github"></i></a>
                    </div>
                </div>`;
        });
    } catch (error) {
        console.warn('Төслүүд ачаалахад алдаа:', error);
    }
}

// 4. Courses харуулах
async function displayCourses() {
    try {
        const snap = await getDocs(collection(db, "courses"));
        const container = document.getElementById('courses-container');
        container.innerHTML = '';
        
        snap.forEach(doc => {
            const c = doc.data();
            container.innerHTML += `
                <div class="course-item">
                    <h3>📚 ${c.name} <span class="semester">${c.semester}</span></h3>
                </div>`;
        });
    } catch (error) {
        console.warn('Хичээлүүд ачаалахад алдаа:', error);
    }
}

// Initialize
(async () => {
    await initSite();
    await displaySkills();
    await displayProjects();
    await displayCourses();
})();
