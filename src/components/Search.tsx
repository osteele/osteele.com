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

		let cleanup: (() => void) | undefined;

		const loadPagefind = async () => {
			try {
				const script = document.createElement("script");
				script.src = "/pagefind/pagefind-ui.js";
				await new Promise<void>((resolve, reject) => {
					script.onload = () => resolve();
					script.onerror = () => reject(new Error("Failed to load Pagefind"));
					document.head.appendChild(script);
				});
				const PagefindUI = (window as Record<string, unknown>).PagefindUI as
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

		return cleanup;
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
				<dialog
					open
					className="fixed inset-0 z-[200] flex items-start justify-center pt-[15vh] bg-black/50 w-full h-full m-0 max-w-none max-h-none border-none"
					onClick={(e) => {
						if (e.target === e.currentTarget) setOpen(false);
					}}
					onKeyDown={() => {}}
					aria-label="Search"
				>
					<div className="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-xl mx-4 p-4 max-h-[70vh] overflow-y-auto">
						<div ref={containerRef} />
					</div>
				</dialog>
			)}
		</>
	);
}
