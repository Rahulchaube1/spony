import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'Vedaix — Cloud Platform & Real-Time Edge Engine',
  description: 'Vedaix Cloud Platform - High-Performance Developer Console, Go Build Engine, and Rust Edge Dispatcher',
  icons: {
    icon: '/vedaix-logo.png',
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" className="dark" suppressHydrationWarning>
      <body className="min-h-screen bg-background text-foreground font-sans antialiased" suppressHydrationWarning>
        {children}
      </body>
    </html>
  );
}
