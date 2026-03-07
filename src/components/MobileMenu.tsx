import { Bars3Icon, XMarkIcon } from "@heroicons/react/24/outline";
import { useState } from "react";

const NAV_LINKS = [
	{ href: "/about", label: "About" },
	{ href: "/projects", label: "Projects" },
	{ href: "/software", label: "Software" },
	{ href: "https://github.com/osteele", label: "GitHub", external: true },
	{ href: "/contact", label: "Contact" },
];

export default function MobileMenu() {
	const [open, setOpen] = useState(false);

	return (
		<div className="sm:hidden">
			<button
				type="button"
				onClick={() => setOpen(!open)}
				aria-label={open ? "Close menu" : "Open menu"}
				aria-expanded={open}
				className="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
			>
				{open ? <XMarkIcon className="w-6 h-6" /> : <Bars3Icon className="w-6 h-6" />}
			</button>

			{open && (
				<div className="absolute top-16 left-0 right-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-lg z-50">
					<nav className="flex flex-col px-4 py-2">
						{NAV_LINKS.map((link) => (
							<a
								key={link.href}
								href={link.href}
								className="py-3 px-2 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md"
								target={link.external ? "_blank" : undefined}
								rel={link.external ? "noopener noreferrer" : undefined}
								onClick={() => setOpen(false)}
							>
								{link.label}
							</a>
						))}
					</nav>
				</div>
			)}
		</div>
	);
}
