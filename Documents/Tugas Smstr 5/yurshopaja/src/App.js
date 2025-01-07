import React, { useState, useEffect } from "react";
import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom"; // Import Navigate untuk redirect
import Navbar from "./components/Navbar"; 
import Header from "./components/Header";
import Features from "./components/Features";
import Testimonials from "./components/Testimonials";
import Contact from "./components/Contact";
import Footer from "./components/Footer";
import WhatsAppButton from "./components/WhatsAppButton";
import Login from "./components/Login";
import Register from "./components/Register";
import { library } from "@fortawesome/fontawesome-svg-core";
import { fab } from "@fortawesome/free-brands-svg-icons";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { auth } from "./firebaseConfig"; // Impor auth dari firebaseConfig
import { onAuthStateChanged } from "firebase/auth"; // Impor untuk mendeteksi status auth

library.add(fab, fas);

function App() {
  const [user, setUser] = useState(null);

  // Memantau status login
  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, (currentUser) => {
      setUser(currentUser); // Jika ada user, set ke state, jika tidak null
    });

    return () => unsubscribe(); // Unsubscribe saat komponen unmount
  }, []);

  return (
    <Router>
      <div className="bg-gray-100">
        <Navbar /> {/* Navbar tetap ditampilkan di semua halaman */}

        <Routes>
          {/* Jika belum login, redirect ke halaman login */}
          {!user ? (
            <Route path="*" element={<Login />} /> // Seluruh akses langsung ke Login jika belum login
          ) : (
            <>
              {/* Jika sudah login, tampilkan halaman lainnya */}
              <Route path="/" element={<Header />} />
              <Route path="/features" element={<Features />} />
              <Route path="/testimonials" element={<Testimonials />} />
              <Route path="/contact" element={<Contact />} />
              <Route path="/register" element={<Register />} />
            </>
          )}

          {/* Untuk akses selain login, jika user belum login */}
          <Route
            path="*"
            element={user ? <Navigate to="/" /> : <Navigate to="/login" />}
          />
        </Routes>

        <Footer />
        <WhatsAppButton />
      </div>
    </Router>
  );
}

export default App;
