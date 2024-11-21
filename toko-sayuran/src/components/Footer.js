import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

const Footer = () => {
  return (
    <footer className="bg-green-700 text-white py-8 relative">
      {/* Dekorasi Latar Belakang */}
      <div className="absolute inset-0 opacity-10">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 1440 320"
          className="w-full h-full"
        >
          <path
            fill="#ffffff"
            fillOpacity="1"
            d="M0,160L48,192C96,224,192,288,288,288C384,288,480,224,576,213.3C672,203,768,245,864,256C960,267,1056,245,1152,213.3C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"
          ></path>
        </svg>
      </div>

      <div className="container mx-auto px-4 relative z-10 text-center">
        {/* Nama Toko */}
        <p className="text-lg font-semibold">
          &copy; 2024 <span className="text-green-300">Yurshop</span>. Semua hak dilindungi.
        </p>

        {/* Media Sosial */}
        <div className="flex justify-center space-x-6 mt-4">
          <a
            href="https://www.instagram.com/fieett_0?igsh=a2V2dmYxa2l5cmY1"
            aria-label="Facebook"
            className="text-gray-300 hover:text-white transition duration-300"
          >
            <FontAwesomeIcon icon={['fab', 'facebook-f']} />
          </a>
          <a
            href="#"
            aria-label="Instagram"
            className="text-gray-300 hover:text-white transition duration-300"
          >
            <FontAwesomeIcon icon={['fab', 'instagram']} />
          </a>
          <a
            href="#"
            aria-label="Twitter"
            className="text-gray-300 hover:text-white transition duration-300"
          >
            <FontAwesomeIcon icon={['fab', 'twitter']} />
          </a>
        </div>

        {/* Pesan Tambahan */}
        <p className="mt-6 text-sm text-gray-300">
          Kami berkomitmen memberikan sayuran segar berkualitas untuk keluarga Anda.
        </p>
      </div>
    </footer>
  );
};

export default Footer;
