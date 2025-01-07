import React, { useState } from "react";
import { auth } from "../firebaseConfig"; // Pastikan file firebaseConfig.js telah mengatur Firebase
import { signInWithEmailAndPassword } from "firebase/auth";
import { useNavigate } from "react-router-dom"; // Impor useNavigate untuk navigasi setelah login

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState(""); // State untuk menampilkan error
  const [loading, setLoading] = useState(false); // State untuk loading
  const navigate = useNavigate(); // Inisialisasi useNavigate untuk navigasi

  const handleLogin = async (e) => {
    e.preventDefault();
    setLoading(true); // Menandakan bahwa proses login sedang berlangsung
    setError(""); // Reset error state sebelum mencoba login

    try {
      await signInWithEmailAndPassword(auth, email, password);
      alert("Login berhasil!");
      navigate("/"); // Navigasi ke halaman utama setelah login berhasil
    } catch (error) {
      setError(error.message); // Set error message jika login gagal
    } finally {
      setLoading(false); // Hentikan loading setelah proses selesai
    }
  };

  return (
    <div className="max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
      <h2 className="text-2xl font-bold mb-4 text-center">Login</h2>
      <form onSubmit={handleLogin} className="space-y-4">
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
            className={`w-full py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 ${loading ? 'opacity-50 cursor-not-allowed' : ''}`}
            disabled={loading} // Menonaktifkan tombol saat proses login berlangsung
          >
            {loading ? "Loading..." : "Login"} {/* Tampilkan loading saat proses login */}
          </button>
        </div>
      </form>
    </div>
  );
};

export default Login; // Gunakan ekspor default di sini
