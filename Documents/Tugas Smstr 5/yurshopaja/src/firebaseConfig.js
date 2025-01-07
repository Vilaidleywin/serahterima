import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";

const firebaseConfig = {
  apiKey: "AIzaSyBv0oJcC3TjSUAG_h7Nbj8LOU8jK9H91JA",
  authDomain: "yur-shop.firebaseapp.com",
  databaseURL: "https://yur-shop-default-rtdb.firebaseio.com",
  projectId: "yur-shop",
  storageBucket: "yur-shop.appspot.com", // Perbaikan di sini
  messagingSenderId: "300772458998",
  appId: "1:300772458998:web:e151e31b5b4013b29587d7"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

export { auth };
