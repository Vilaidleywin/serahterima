import React, { useState, useEffect, useRef } from 'react';

const Contact = () => {
  const [formVisible, setFormVisible] = useState(false);
  const [successMessageVisible, setSuccessMessageVisible] = useState(false);
  const contactRef = useRef(null);
  const formRef = useRef(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        setFormVisible(entry.isIntersecting); // Set visible jika bagian kontak terlihat
      },
      { threshold: 0.3 } // Berikan nilai threshold untuk mendeteksi 30% dari elemen
    );

    if (contactRef.current) {
      observer.observe(contactRef.current);
    }

    return () => {
      if (contactRef.current) {
        observer.unobserve(contactRef.current);
      }
    };
  }, []);

  const handleSubmit = async (event) => {
    event.preventDefault();
    const formData = new FormData(formRef.current);

    try {
      const response = await fetch(formRef.current.action, {
        method: "POST",
        body: formData,
        headers: {
          Accept: "application/json",
        },
      });

      if (response.ok) {
        setSuccessMessageVisible(true);
        formRef.current.reset();
      } else {
        alert("Gagal mengirim formulir. Silakan coba lagi.");
      }
    } catch (error) {
      console.error("Terjadi kesalahan:", error);
      alert("Terjadi kesalahan. Silakan coba lagi.");
    }
  };

  return (
    <section
      id="contact"
      ref={contactRef}
      className={`bg-gray-100 py-16 transition-opacity duration-500 ${
        formVisible ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-10'
      }`}
    >
      <div className="container mx-auto px-6 lg:px-12">
        <h2 className="text-4xl font-extrabold text-center mb-8 text-gray-800">
          Hubungi Kami
        </h2>
        <p className="text-center text-gray-600 mb-10">
          Jika Anda memiliki pertanyaan, saran, atau kritik, silakan hubungi kami menggunakan formulir di bawah ini.
        </p>
        {successMessageVisible && (
          <div id="success-message" className="text-green-600 text-center mb-4">
            Formulir berhasil dikirim!
          </div>
        )}
        <form
          ref={formRef}
          action="https://formspree.io/f/mldellqb"
          method="POST"
          onSubmit={handleSubmit}
          className="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg"
        >
          <div className="mb-6">
            <label
              htmlFor="name"
              className="block text-sm font-medium text-gray-700 mb-1"
            >
              Nama
            </label>
            <input
              type="text"
              name="name"
              id="name"
              placeholder="Masukkan nama Anda"
              className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-green-500 focus:ring focus:ring-green-200 transition"
              required
            />
          </div>
          <div className="mb-6">
            <label
              htmlFor="email"
              className="block text-sm font-medium text-gray-700 mb-1"
            >
              Email
            </label>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="Masukkan email Anda"
              className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-green-500 focus:ring focus:ring-green-200 transition"
              required
            />
          </div>
          <div className="mb-6">
            <label
              htmlFor="message"
              className="block text-sm font-medium text-gray-700 mb-1"
            >
              Pesan
            </label>
            <textarea
              name="message"
              id=" message"
              placeholder="Tulis pesan Anda di sini"
              className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-green-500 focus:ring focus:ring-green-200 transition"
              rows="4"
              required
            ></textarea>
          </div>
          <button
            type="submit"
            className="w-full bg-green-500 text-white font-bold py-3 rounded-lg hover:bg-green-600 transition"
          >
            Kirim
          </button>
        </form>
      </div>
    </section>
  );
};

export default Contact;