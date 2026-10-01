export interface LocalNavigationItem {
	label: string;
	href: string;
	activePaths: readonly string[];
}

export const localNavigation: readonly LocalNavigationItem[] = [
	{
		label: "Work",
		href: "/work",
		activePaths: ["/work", "/products", "/topics"],
	},
	{
		label: "Software",
		href: "/software",
		activePaths: ["/software"],
	},
	{
		label: "Teaching",
		href: "/teaching",
		activePaths: ["/teaching"],
	},
	{
		label: "About",
		href: "/about",
		activePaths: ["/about", "/contact", "/uses", "/colophon"],
	},
];

export function matchesNavigationPath(pathname: string, item: LocalNavigationItem): boolean {
	return item.activePaths.some((path) => pathname === path || pathname.startsWith(`${path}/`));
}
