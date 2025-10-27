import React, { useState, useEffect } from "react";

function WhatsAppButton() {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    // Menampilkan tombol WhatsApp setelah komponen di-mount
    const timer = setTimeout(() => {
      setIsVisible(true);
    }, 100); // Tambahkan sedikit delay untuk animasi (opsional)

    return () => clearTimeout(timer); // Bersihkan timeout jika komponen di-unmount
  }, []);

  return (
    <div
      className={`fixed bottom-5 right-5 z-50 group transition-opacity duration-1000 ${
        isVisible ? "opacity-100" : "opacity-0"
      }`}
    >
      {/* Tombol WhatsApp */}
      <a
        href="https://wa.me/087771694295" // Ganti dengan nomor WhatsApp Anda
        target="_blank"
        rel="noopener noreferrer"
        className="block w-16 h-16 bg-green-500 rounded-full shadow-lg hover:bg-green-600 transition-all"
      >
        <img
          src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg"
          alt="Hubungi Kami"
          className="w-full h-full p-2"
        />
      </a>

      {/* Tooltip di sebelah kanan */}
      <span className="absolute top-1/2 -translate-y-1/2 right-20 w-max px-3 py-2 text-sm font-semibold text-white bg-black rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        WhatsApp Kami
      </span>
    </div>
  );
}

export default WhatsAppButton;
