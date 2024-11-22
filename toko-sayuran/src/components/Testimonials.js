import React, { useState, useEffect } from 'react';

const testimonialsData = [
  {
    name: 'Ani',
    location: 'Jakarta',
    message: 'Saya sangat puas dengan sayuran yang saya beli. Segar dan berkualitas tinggi!',
    image: 'kodok.jpg',
  },
  {
    name: 'Budi',
    location: 'Bandung',
    message: 'Pengiriman sangat cepat dan sayurannya selalu dalam kondisi terbaik!',
    image: 'kodok.jpg',
  },
  {
    name: 'Citra',
    location: 'Surabaya',
    message: 'Harga yang ditawarkan sangat terjangkau, saya pasti akan kembali lagi!',
    image: 'kodok.jpg',
  },
];

const Testimonials = () => {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => setIsVisible(entry.isIntersecting),
      { threshold: 0.2 }
    );

    const section = document.getElementById('testimonials');
    if (section) observer.observe(section);

    return () => {
      if (section) observer.unobserve(section);
    };
  }, []);

  return (
    <section
      id="testimonials"
      className={`py-10 bg-gray-100 transition-opacity duration-1000 ${
        isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'
      }`}
    >
      <div className="container mx-auto px-4">
        <h2 className="text-4xl font-bold text-center mb-8 text-gray-800">
          Apa Kata Pelanggan Kami
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {testimonialsData.map((testimonial, index) => (
            <div
              key={index}
              className="bg-white shadow-lg rounded-lg p-6 transition-transform duration-300 hover:scale-105 hover:shadow-xl"
            >
              <img
                src={testimonial.image}
                alt={testimonial.name}
                className="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-green-500"
              />
              <p className="text-gray-600 italic text-center">"{testimonial.message}"</p>
              <p className="mt-4 font-semibold text-center">
                - {testimonial.name}, {testimonial.location}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Testimonials;
