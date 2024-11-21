import React from 'react';
import Navbar from './components/Navbar';
import Header from './components/Header';
import Features from './components/Features';
import Testimonials from './components/Testimonials';
import Contact from './components/Contact';
import Footer from './components/Footer';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fab } from '@fortawesome/free-brands-svg-icons'; // Ikon media sosial
import { fas } from '@fortawesome/free-solid-svg-icons'; // Ikon lainnya (opsional)

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
    </div>
  );
}

export default App;