import React, { useEffect, useState } from 'react';

const featuresData = [
  { emoji: '🌱', title: 'Sayuran Organik', description: 'Kami menyediakan sayuran organik yang segar dan berkualitas tinggi.' },
  { emoji: '🚚', title: 'Pengiriman Cepat', description: 'Pengiriman cepat untuk memastikan sayuran sampai di tangan Anda dalam keadaan segar.' },
  { emoji: '💰', title: 'Harga Terjangkau', description: 'Kami menawarkan harga yang bersaing untuk semua produk kami.' },
];

const Features = () => {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        setIsVisible(entry.isIntersecting);
      },
      { threshold: 0.1 } // Elemen terlihat minimal 10%
    );

    const section = document.getElementById('features');
    if (section) observer.observe(section);

    return () => {
      if (section) observer.unobserve(section);
    };
  }, []);

  return (
    <section
      id="features"
      className={`py-10 cursor-default transition-opacity duration-1000 ${
        isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'
      }`}
    >
      <div className="container mx-auto px-4">
        <h2 className="text-4xl font-bold text-center mb-6">Fitur Kami</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {featuresData.map((feature, index) => (
            <div
              key={index}
              className="bg-white shadow-lg rounded-lg p-6 transform transition-transform duration-300 hover:scale-105 hover:shadow-xl"
            >
              <h3 className="text-2xl font-semibold flex items-center space-x-2">
                <span>{feature.emoji}</span>
                <span>{feature.title}</span>
              </h3>
              <p className="mt-2 text-gray-700">{feature.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Features;
