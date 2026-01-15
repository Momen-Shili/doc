<php if ($msg = flash('success')): >
<div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200" role="status" aria-live="polite">
    <div class="flex items-start gap-3">
        <svg class="mt-0.5 h-5 w-5 text-emerald-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.172 7.707 8.879A1 1 0 006.293 10.293l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <div><= e($msg) ></div>
    </div>
</div>
<php endif; >