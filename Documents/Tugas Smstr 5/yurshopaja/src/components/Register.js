import React, { useState } from "react";
import { auth } from "../firebaseConfig"; // Pastikan file firebaseConfig.js telah mengatur Firebase
import { createUserWithEmailAndPassword } from "firebase/auth";

const Register = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState(""); // State untuk menampilkan error
  const [loading, setLoading] = useState(false); // State untuk loading

  const handleRegister = async (e) => {
    e.preventDefault();
    setLoading(true); // Menandakan bahwa proses registrasi sedang berlangsung
    setError(""); // Reset error state sebelum mencoba registrasi

    try {
      await createUserWithEmailAndPassword(auth, email, password);
      alert("Registrasi berhasil!");
      setLoading(false); // Menandakan registrasi berhasil, hentikan loading
    } catch (error) {
      setError(error.message); // Set error message jika registrasi gagal
      setLoading(false); // Hentikan loading jika terjadi error
    }
  };

  return (
    <div className="max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
      <h2 className="text-2xl font-bold mb-4">Register</h2>
      <form onSubmit={handleRegister} className="space-y-4">
        <div>
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-md"
            required
          />
        </div>
        <div>
          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-md"
            required
          />
        </div>
        {error && (
          <div className="text-red-500 text-sm">{error}</div> // Menampilkan pesan error jika ada
        )}
        <div>
          <button
            type="submit"
            className={`w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600 ${loading ? 'opacity-50 cursor-not-allowed' : ''}`}
            disabled={loading} // Menonaktifkan tombol saat proses registrasi berlangsung
          >
            {loading ? "Loading..." : "Register"} {/* Tampilkan loading saat proses registrasi */}
          </button>
        </div>
      </form>
    </div>
  );
};

export default Register; // Gunakan ekspor default di sini
