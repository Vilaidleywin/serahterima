import React from "react"; // Pastikan hanya ada satu deklarasi
import Navbar from "./components/Navbar";
import Header from "./components/Header";
import Features from "./components/Features";
import Testimonials from "./components/Testimonials";
import Contact from "./components/Contact";
import Footer from "./components/Footer";
import WhatsAppButton from "./components/WhatsAppButton"; // Impor tombol WhatsApp
import { library } from "@fortawesome/fontawesome-svg-core";
import { fab } from "@fortawesome/free-brands-svg-icons";
import { fas } from "@fortawesome/free-solid-svg-icons";

library.add(fab, fas);

function App() {
  return (
    <div className="bg-gray-100">
      <Navbar />
      <Header />
      <Features />
      <Testimonials />
      <Contact />
      <Footer />
      <WhatsAppButton />
    </div>
  );
}

export default App;
