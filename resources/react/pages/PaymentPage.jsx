import React, { useEffect, useState } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';

const PaymentPage = () => {
    const { orderCode } = useParams();
    const navigate = useNavigate();
    const location = useLocation();
    
    // Token yang dikirim via routing state dari halaman checkout
    const snapToken = location.state?.snapToken;
    const [paymentStatus, setPaymentStatus] = useState('waiting'); // waiting, success, error, pending

    useEffect(() => {
        if (!snapToken) {
            // Jika masuk ke halaman ini langsung via URL tanpa token, lempar ke menu
            navigate('/menu', { replace: true });
            return;
        }

        // Pastikan Midtrans script sudah load (ditaruh di index.html/app.blade.php)
        if (window.snap) {
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    setPaymentStatus('success');
                    // Arahkan ke halaman status pesanan
                    setTimeout(() => {
                        navigate(`/order/${orderCode}`, { replace: true });
                    }, 2000);
                },
                onPending: function(result) {
                    setPaymentStatus('pending');
                    // Bisa diarahkan ke status pesanan juga untuk dipantau
                    setTimeout(() => {
                        navigate(`/order/${orderCode}`, { replace: true });
                    }, 2000);
                },
                onError: function(result) {
                    setPaymentStatus('error');
                },
                onClose: function() {
                    // User menutup popup sebelum bayar
                    setPaymentStatus('closed');
                }
            });
        } else {
            console.error('Midtrans snap.js not loaded!');
            setPaymentStatus('error');
        }
    }, [snapToken, orderCode, navigate]);

    return (
        <div className="flex flex-col items-center justify-center min-h-[60vh] text-center page-enter-active">
            <h1 className="text-2xl font-bold mb-4">Selesaikan Pembayaran</h1>
            <p className="text-muted mb-8 max-w-md">
                Kode Pesanan: <span className="font-mono text-white">{orderCode}</span>
            </p>

            {paymentStatus === 'waiting' && (
                <div className="animate-pulse flex flex-col items-center">
                    <div className="w-16 h-16 border-4 border-brand border-t-transparent rounded-full animate-spin mb-4"></div>
                    <p className="text-brand">Menunggu proses pembayaran pada popup Midtrans...</p>
                </div>
            )}

            {paymentStatus === 'success' && (
                <div className="text-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-20 w-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 className="text-xl font-bold text-white mb-2">Pembayaran Berhasil!</h2>
                    <p>Mengarahkan ke status pesanan...</p>
                </div>
            )}

            {paymentStatus === 'closed' && (
                <div className="bg-card p-6 rounded-2xl border border-subtle max-w-sm w-full">
                    <h2 className="text-lg font-bold text-white mb-2">Pembayaran Dibatalkan</h2>
                    <p className="text-sm text-muted mb-6">Anda belum menyelesaikan pembayaran. Pesanan akan kadaluarsa jika tidak dibayar.</p>
                    <button 
                        onClick={() => window.location.reload()}
                        className="w-full py-3 bg-brand text-white rounded-xl font-medium mb-3"
                    >
                        Coba Bayar Lagi
                    </button>
                    <button 
                        onClick={() => navigate('/menu')}
                        className="w-full py-3 bg-surface border border-subtle text-white rounded-xl font-medium"
                    >
                        Kembali ke Menu
                    </button>
                </div>
            )}
            
            {paymentStatus === 'error' && (
                <div className="text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-20 w-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 className="text-xl font-bold text-white mb-2">Terjadi Kesalahan</h2>
                    <p>Gagal memuat sistem pembayaran Midtrans.</p>
                </div>
            )}
        </div>
    );
};

export default PaymentPage;
