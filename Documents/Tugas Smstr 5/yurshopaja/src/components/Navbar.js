import React, { useState, useEffect } from 'react';

const Navbar = () => {
  const [isVisible, setIsVisible] = useState(true);
  const [lastScrollY, setLastScrollY] = useState(0);
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      const currentScrollY = window.scrollY;

      if (currentScrollY > lastScrollY && currentScrollY > 50) {
        setIsVisible(false); // Sembunyikan navbar saat scroll ke bawah
      } else {
        setIsVisible(true); // Tampilkan navbar saat scroll ke atas
      }

      setLastScrollY(currentScrollY);
    };

    window.addEventListener('scroll', handleScroll);

    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  }, [lastScrollY]);

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  return (
    <>
      <nav
        className={`fixed w-full z-50 top-0 left-0 transition-transform duration-300 ${
          isVisible ? 'translate-y-0' : '-translate-y-full'
        } bg-green-700 shadow-lg`}
        style={{ height: '60px' }}
      >
        <div className="container mx-auto px-4 flex justify-between items-center h-full">
          {/* Logo */}
          <div className="text-2xl font-bold text-white">
            Yur <span className="text-green-300">Shop</span>
          </div>

          {/* Hamburger Menu Button */}
          <button
            className="text-white md:hidden focus:outline-none"
            onClick={toggleMenu}
          >
            {isMenuOpen ? (
              <svg
                className="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M6 18L18 6M6 6l12 12"
                ></path>
              </svg>
            ) : (
              <svg
                className="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M4 6h16M4 12h16m-7 6h7"
                ></path>
              </svg>
            )}
          </button>

          {/* Navigasi */}
          <ul
            className={`md:flex md:space-x-6 md:static absolute transform left-0 w-full md:w-auto bg-green-700 transition-all duration-300 ${
              isMenuOpen ? 'top-[60px] translate-y-0' : '-translate-y-full md:translate-y-0'
            }`}
          >
            <li>
              <a
                href="#"
                className="block py-2 px-4 text-white hover:text-green-300 transition duration-300"
                onClick={() => setIsMenuOpen(false)}
              >
                Beranda
              </a>
            </li>
            <li>
              <a
                href="#features"
                className="block py-2 px-4 text-white hover:text-green-300 transition duration-300"
                onClick={() => setIsMenuOpen(false)}
              >
                Fitur
              </a>
            </li>
            <li>
              <a
                href="#testimonials"
                className="block py-2 px-4 text-white hover:text-green-300 transition duration-300"
                onClick={() => setIsMenuOpen(false)}
              >
                Testimonial
              </a>
            </li>
            <li>
              <a
                href="#contact"
                className="block py-2 px-4 text-white hover:text-green-300 transition duration-300"
                onClick={() => setIsMenuOpen(false)}
              >
                Kontak
              </a>
            </li>
          </ul>

          {/* Tombol Login dan Register */}
          <div className="hidden md:flex space-x-4">
            <a
              href="/login"  // Ganti dengan path yang sesuai untuk halaman login
              className="py-2 px-4 bg-white text-green-700 rounded-lg shadow hover:bg-green-100 transition duration-300"
            >
              Login
            </a>
            <a
              href="/register"  // Ganti dengan path yang sesuai untuk halaman register
              className="py-2 px-4 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition duration-300"
            >
              Register
            </a>
          </div>
        </div>
      </nav>

      {/* Tambahkan padding di konten untuk memberi ruang bagi navbar */}
      <div style={{ marginTop: '60px' }}>
        {/* Konten lainnya */}
      </div>
    </>
  );
};

export default Navbar;
