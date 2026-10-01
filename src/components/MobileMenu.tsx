import { localNavigation, matchesNavigationPath } from "@/data/navigation";
import { Bars3Icon, XMarkIcon } from "@heroicons/react/24/outline";
import { useEffect, useState } from "react";

interface MobileMenuProps {
	currentPath: string;
}

export default function MobileMenu({ currentPath }: MobileMenuProps) {
	const [open, setOpen] = useState(false);
	const [pathname, setPathname] = useState(currentPath);

	useEffect(() => {
		const updatePathname = () => {
			setPathname(window.location.pathname);
			setOpen(false);
		};
		document.addEventListener("astro:after-swap", updatePathname);
		return () => document.removeEventListener("astro:after-swap", updatePathname);
	}, []);

	return (
		<div className="md:hidden">
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
						<p className="px-2 pt-2 pb-1 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">This site</p>
						{localNavigation.map((link) => (
							<a
								key={link.href}
								href={link.href}
								aria-current={matchesNavigationPath(pathname, link) ? "page" : undefined}
								className="rounded-md px-2 py-3 text-gray-700 hover:bg-gray-50 aria-[current=page]:font-semibold aria-[current=page]:text-[#D9482A] dark:text-gray-300 dark:hover:bg-gray-800 dark:aria-[current=page]:text-[#FF8A6B]"
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
