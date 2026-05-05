// Firebase SDK-уудыг импортлох
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getFirestore, collection, getDocs, doc, getDoc, addDoc, updateDoc, deleteDoc, query, where } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";
import { getAuth, signInWithEmailAndPassword, onAuthStateChanged, signOut, createUserWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-storage.js";

// Таны Firebase төслийн тохиргоо (Firebase Console -> Project Settings-ээс хуулж авна)
const firebaseConfig = {
    apiKey: "AIzaSyDd0c2cLm67KWZtGx_tPVnN7YSAVJJ_ARY",
    authDomain: "web-fd32d.firebaseapp.com",
    projectId: "web-fd32d",
    storageBucket: "web-fd32d.firebasestorage.app",
    messagingSenderId: "834539335338",
    appId: "1:834539335338:web:1a8ec4855541835607aad3",
    measurementId: "G-058798RFDF"
  };
// Initialize Firebase
const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const auth = getAuth(app);
const storage = getStorage(app);

export { 
    db, collection, getDocs, doc, getDoc, addDoc, updateDoc, deleteDoc, query, where,
    auth, signInWithEmailAndPassword, onAuthStateChanged, signOut, createUserWithEmailAndPassword,
    storage, ref, uploadBytes, getDownloadURL
};
