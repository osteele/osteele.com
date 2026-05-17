import { MagnifyingGlassIcon } from "@heroicons/react/24/outline";
import { useEffect, useRef, useState } from "react";

export default function Search() {
	const [open, setOpen] = useState(false);
	const containerRef = useRef<HTMLDivElement>(null);

	useEffect(() => {
		const onKeyDown = (e: KeyboardEvent) => {
			if ((e.metaKey || e.ctrlKey) && e.key === "k") {
				e.preventDefault();
				setOpen((prev) => !prev);
			}
			if (e.key === "Escape") setOpen(false);
		};
		document.addEventListener("keydown", onKeyDown);
		return () => document.removeEventListener("keydown", onKeyDown);
	}, []);

	useEffect(() => {
		if (!open) return;

		const loadPagefind = async () => {
			try {
				const script = document.createElement("script");
				script.src = "/pagefind/pagefind-ui.js";
				await new Promise<void>((resolve, reject) => {
					script.onload = () => resolve();
					script.onerror = () => reject(new Error("Failed to load Pagefind"));
					document.head.appendChild(script);
				});
				const PagefindUI = (window as unknown as Record<string, unknown>).PagefindUI as
					| (new (opts: { element: HTMLElement; showSubResults: boolean }) => unknown)
					| undefined;
				if (PagefindUI && containerRef.current && containerRef.current.childElementCount === 0) {
					new PagefindUI({
						element: containerRef.current,
						showSubResults: true,
					});
				}
				const input = containerRef.current?.querySelector<HTMLInputElement>("input");
				input?.focus();
			} catch {
				if (containerRef.current && containerRef.current.childElementCount === 0) {
					containerRef.current.innerHTML =
						'<p class="text-gray-500 text-center py-8">Search is available after building the site (bun run build).</p>';
				}
			}
		};
		loadPagefind();
	}, [open]);

	return (
		<>
			<button
				type="button"
				onClick={() => setOpen(true)}
				aria-label="Search"
				className="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
			>
				<MagnifyingGlassIcon className="w-5 h-5" />
			</button>

			{open && (
				<div className="fixed inset-0 z-[200] bg-black/50 px-4 pt-[15vh]">
					<button
						type="button"
						aria-label="Close search"
						tabIndex={-1}
						className="absolute inset-0 h-full w-full cursor-default bg-transparent"
						onClick={() => setOpen(false)}
					/>
					{/* biome-ignore lint/a11y/useSemanticElements: Safari mispositions the native dialog backdrop here. */}
					<div
						role="dialog"
						aria-modal="true"
						aria-label="Search"
						className="relative mx-auto max-h-[70vh] w-full max-w-xl overflow-y-auto rounded-xl bg-white p-4 shadow-2xl dark:bg-gray-900"
					>
						<div ref={containerRef} />
					</div>
				</div>
			)}
		</>
	);
}
