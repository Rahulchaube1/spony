import Link from 'next/link';

export default function NotFound() {
  return (
    <div className="min-h-screen bg-background text-foreground flex items-center justify-center p-6 font-mono">
      <div className="border border-border bg-card p-6 max-w-md w-full space-y-4 text-center">
        <h2 className="text-xl font-bold">404 — PAGE NOT FOUND</h2>
        <p className="text-xs text-muted-foreground">
          The requested route does not exist on this Vedaix instance.
        </p>
        <Link
          href="/"
          className="inline-block border border-primary bg-primary px-4 py-2 text-xs font-bold text-primary-foreground hover:opacity-90"
        >
          Return to Dashboard
        </Link>
      </div>
    </div>
  );
}
