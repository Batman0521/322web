import { db, doc, getDoc, collection, getDocs } from './firebase.js';

// 1. Сэтгэц (Settings)
async function initSite() {
    const docSnap = await getDoc(doc(db, "siteSettings", "main"));
    if (docSnap.exists()) {
        const d = docSnap.data();
        document.getElementById('hero-title').innerText = d.heroTitle;
        document.getElementById('hero-subtitle').innerText = d.heroSubtitle;
        document.getElementById('cv-link').href = d.cvUrl;
        document.getElementById('footer-text').innerText = d.footerText;
    }
}

// 2. Skills харуулах
async function displaySkills() {
    const snap = await getDocs(collection(db, "skills"));
    const container = document.getElementById('skills-container');
    snap.forEach(doc => {
        const s = doc.data();
        container.innerHTML += `
            <div class="card skill-card">
                <div class="icon">${s.icon}</div>
                <h3>${s.name}</h3>
                <span class="badge">${s.level}</span>
            </div>`;
    });
}

// 3. Projects харуулах
async function displayProjects() {
    const snap = await getDocs(collection(db, "projects"));
    const container = document.getElementById('projects-container');
    snap.forEach(doc => {
        const p = doc.data();
        container.innerHTML += `
            <div class="card project-card">
                <img src="${p.imageUrl}" alt="${p.title}" onerror="this.src='assets/images/placeholder.png'">
                <div class="p-content">
                    <h3>${p.title}</h3>
                    <p>${p.shortDescription}</p>
                    <small>Технологи: ${p.techStack}</small>
                    <a href="${p.githubUrl}" target="_blank">Линк <i class="fa-brands fa-github"></i></a>
                </div>
            </div>`;
    });
}

// 4. Courses харуулах
async function displayCourses() {
    const snap = await getDocs(collection(db, "courses"));
    const container = document.getElementById('courses-container');
    snap.forEach(doc => {
        const c = doc.data();
        container.innerHTML += `
            <div class="course-item">
                <h3>${c.name} - <span>${c.semester}</span></h3>
            </div>`;
    });
}

initSite();
displaySkills();
displayProjects();
displayCourses();