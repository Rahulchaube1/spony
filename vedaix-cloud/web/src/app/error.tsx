'use client';

import React, { useEffect } from 'react';
import { IconAlertTriangle, IconRefresh } from '@tabler/icons-react';

export default function ErrorBoundary({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    console.error('Captured by Next.js ErrorBoundary:', error);
  }, [error]);

  return (
    <div className="min-h-screen bg-background text-foreground flex items-center justify-center p-6 font-mono">
      <div className="w-full max-w-xl border border-border bg-card p-6 space-y-4">
        <div className="flex items-center gap-2.5 border-b border-border pb-3">
          <IconAlertTriangle className="size-5 text-destructive" />
          <h2 className="text-sm font-bold tracking-tight">APPLICATION RUNTIME RECOVERY</h2>
        </div>
        <p className="text-xs text-muted-foreground">
          An unexpected error occurred during execution. The diagnostic details below have been isolated:
        </p>
        <div className="border border-border bg-background p-3 text-xs overflow-auto max-h-48 text-destructive">
          {error?.message || 'Unknown runtime error'}
          {error?.stack && (
            <pre className="mt-2 text-[10px] text-muted-foreground whitespace-pre-wrap">{error.stack}</pre>
          )}
        </div>
        <div className="flex items-center justify-between pt-2">
          <span className="text-[10px] text-muted-foreground">Digest: {error?.digest || 'N/A'}</span>
          <button
            onClick={() => reset()}
            className="flex items-center gap-1.5 border border-primary bg-primary px-3 py-1.5 text-xs font-bold text-primary-foreground hover:opacity-90 transition-opacity"
          >
            <IconRefresh className="size-3.5" />
            <span>Retry Operation</span>
          </button>
        </div>
      </div>
    </div>
  );
}
