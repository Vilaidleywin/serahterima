import React, { useState, useEffect } from 'react';

const Navbar = () => {
  const [isVisible, setIsVisible] = useState(true);
  const [lastScrollY, setLastScrollY] = useState(0);

  useEffect(() => {
    const handleScroll = () => {
      const currentScrollY = window.scrollY;

      if (currentScrollY > lastScrollY && currentScrollY > 50) {
        // Jika scroll ke bawah, sembunyikan navbar
        setIsVisible(false);
      } else {
        // Jika scroll ke atas, tampilkan navbar
        setIsVisible(true);
      }

      setLastScrollY(currentScrollY);
    };

    window.addEventListener('scroll', handleScroll);

    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  }, [lastScrollY]);

  return (
    <nav
      className={`fixed w-full z-50 top-0 left-0 transition-transform duration-300 ${
        isVisible ? 'translate-y-0' : '-translate-y-full'
      } bg-green-700 shadow-lg`}
    >
      <div className="container mx-auto px-4 flex justify-between items-center py-4">
        {/* Logo */}
        <div className="text-2xl font-bold text-white">
          Yur <span className="text-green-300">Shop</span>
        </div>

        {/* Navigasi */}
        <ul className="flex space-x-6">
          <li>
            <a
              href="#"
              className="text-white hover:text-green-300 transition duration-300"
            >
              Beranda
            </a>
          </li>
          <li>
            <a
              href="#features"
              className="text-white hover:text-green-300 transition duration-300"
            >
              Fitur
            </a>
          </li>
          <li>
            <a
              href="#testimonials"
              className="text-white hover:text-green-300 transition duration-300"
            >
              Testimonial
            </a>
          </li>
          <li>
            <a
              href="#contact"
              className="text-white hover:text-green-300 transition duration-300"
            >
              Kontak
            </a>
          </li>
        </ul>
      </div>
    </nav>
  );
};

export default Navbar;
