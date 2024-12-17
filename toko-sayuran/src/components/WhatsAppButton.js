import React, { useState, useEffect } from "react";

function WhatsAppButton() {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const timer = setTimeout(() => {
      setIsVisible(true);
    }, 200);

    return () => clearTimeout(timer);
  }, []);

  return (
    <div
      className={`fixed bottom-5 right-5 z-50 transition-all duration-1000 ${
        isVisible ? "opacity-100 scale-100" : "opacity-0 scale-90"
      }`}
    >
      <div className="relative group">
        <a
          href="https://wa.me/6287771694295"
          target="_blank"
          rel="noopener noreferrer"
          className="flex items-center justify-center w-16 h-16 bg-green-500 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 transform hover:scale-110"
          aria-label="Hubungi kami via WhatsApp"
        >
          <img
            src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg"
            alt="WhatsApp Logo"
            className="w-10 h-10"
          />
        </a>

        {/* Tooltip yang lebih modern */}
        <span className="absolute bottom-20 left-1/2 transform -translate-x-1/2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow-lg transition-opacity duration-300 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 translate-y-2">
          Hubungi Kami
        </span>
      </div>
    </div>
  );
}

export default WhatsAppButton;