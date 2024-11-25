import React from 'react';

const Header = () => {
  return (
    <header className="bg-green-700 text-white text-center py-20 relative overflow-hidden">
      {/* Dekorasi Latar Belakang */}
      <div className="absolute inset-0 opacity-20">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 1440 320"
          className="w-full h-full"
        >
          <path
            fill="#ffffff"
            fillOpacity="1"
            d="M0,160L48,176C96,192,192,224,288,218.7C384,213,480,171,576,165.3C672,160,768,192,864,208C960,224,1056,224,1152,213.3C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"
          ></path>
        </svg>
      </div>

      {/* Konten Header */}
      <div className="relative z-10">
        <h1 className="text-5xl lg:text-6xl font-bold drop-shadow-md">
          Selamat Datang di YurShop
        </h1>
        <p className="mt-4 text-lg lg:text-xl text-gray-200">
          Sayuran terbaik untuk kesehatan Anda
        </p>
        <a
          href="linktr.ee/fieett_0"
          className="mt-8 inline-block bg-white text-green-600 font-semibold py-3 px-8 rounded-lg shadow-lg transition-transform duration-300 hover:bg-green-600 hover:text-white transform hover:scale-105"
        >
          Hubungi Kami
        </a>
      </div>

      {/* Dekorasi Lingkaran */}
      <div className="absolute top-10 left-10 w-48 h-48 bg-white bg-opacity-10 rounded-full blur-3xl"></div>
      <div className="absolute bottom-10 right-10 w-72 h-72 bg-white bg-opacity-20 rounded-full blur-2xl"></div>
    </header>
  );
};

export default Header;
