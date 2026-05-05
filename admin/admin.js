import { updateDoc, doc } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

// Site Settings-ийг шинэчлэх
const settingsForm = document.getElementById('settings-form');
settingsForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const ref = doc(db, "siteSettings", "main");
    await updateDoc(ref, {
        heroTitle: document.getElementById('set-title').value,
        heroSubtitle: document.getElementById('set-subtitle').value
    });
    alert("Тохиргоо хадгалагдлаа!");
});