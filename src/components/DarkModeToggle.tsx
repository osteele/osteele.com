import { MoonIcon, SunIcon } from "@heroicons/react/24/outline";
import { useEffect, useState } from "react";

export default function DarkModeToggle() {
	const [dark, setDark] = useState(false);

	useEffect(() => {
		setDark(document.documentElement.classList.contains("dark"));

		const handler = () => {
			setDark(document.documentElement.classList.contains("dark"));
		};
		document.addEventListener("astro:after-swap", handler);
		return () => document.removeEventListener("astro:after-swap", handler);
	}, []);

	const toggle = () => {
		const next = !dark;
		document.documentElement.classList.toggle("dark", next);
		localStorage.setItem("theme", next ? "dark" : "light");
		setDark(next);
	};

	return (
		<button
			type="button"
			onClick={toggle}
			aria-label={dark ? "Switch to light mode" : "Switch to dark mode"}
			className="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
		>
			{dark ? <SunIcon className="w-5 h-5" /> : <MoonIcon className="w-5 h-5" />}
		</button>
	);
}
