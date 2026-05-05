import { getAuth, signInWithEmailAndPassword, onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

const auth = getAuth();

// 1. Нэвтрэх функц (Form ашиглан дуудаж болно)
async function login(email, password) {
    try {
        await signInWithEmailAndPassword(auth, email, password);
        console.log("Амжилттай нэвтэрлээ");
    } catch (error) {
        alert("Нэвтрэхэд алдаа гарлаа: " + error.message);
    }
}

// 2. Нэвтрээгүй бол Admin-аас хөөх эсвэл контентыг нуух
onAuthStateChanged(auth, (user) => {
    if (user) {
        console.log("Admin нэвтэрсэн байна");
        document.getElementById('admin-content').style.display = 'block';
        document.getElementById('login-form').style.display = 'none';
    } else {
        document.getElementById('admin-content').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
    }
});