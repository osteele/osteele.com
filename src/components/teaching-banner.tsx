export function TeachingBanner() {
	return (
		<div className="relative mb-12">
			<div className="absolute inset-0 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30 -z-10" />
			<div className="container py-12 relative">
				<div className="relative z-10">
					<h1 className="text-5xl md:text-6xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">
						Teaching & Education
					</h1>
					<p className="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-2xl leading-relaxed">
						Exploring the intersection of technology, design, and learning through hands-on courses and educational
						resources.
					</p>
				</div>

				<div className="absolute right-0 -top-1 w-72 h-72 opacity-10 dark:opacity-5">
					<svg viewBox="0 0 200 200" className="w-full h-full text-blue-600">
						<path
							fill="currentColor"
							opacity="0.3"
							d="M45.3,-59.1C58.9,-51.1,70.3,-37.7,75.2,-22.1C80.1,-6.5,78.5,11.2,71.3,26.3C64.1,41.4,51.3,53.8,36.5,61.5C21.7,69.2,4.9,72.1,-11.1,69.7C-27.1,67.3,-42.3,59.5,-54.1,47.7C-65.9,35.9,-74.3,20,-76.1,3C-77.9,-14,-73.1,-31.1,-62.3,-43.6C-51.5,-56.1,-34.7,-64,-18.1,-67.7C-1.5,-71.3,14.9,-70.7,29.8,-67.1C44.8,-63.5,58.3,-56.9,45.3,-59.1Z"
							transform="translate(100 100)"
						/>
						<path fill="currentColor" d="M100 40l-50 25 50 25 50-25-50-25zM60 80v20l40 20 40-20v-20l-40 20-40-20z" />
						<path fill="currentColor" d="M50 120h30c5.5 0 10 4.5 10 10v20H50v-30z" />
						<path fill="currentColor" d="M150 120h-30c-5.5 0-10 4.5-10 10v20h40v-30z" />
						<path fill="none" stroke="currentColor" strokeWidth="4" d="M90 130c0-5.5 4.5-10 10-10s10 4.5 10 10" />
						<path fill="currentColor" transform="rotate(45, 140, 60)" d="M130 50h20l5 5-25 25-5-5z" />
						<path
							d="M40 70l-10 10 10 10M60 70l10 10-10 10"
							stroke="currentColor"
							strokeWidth="4"
							strokeLinecap="round"
							strokeLinejoin="round"
							fill="none"
						/>
					</svg>
				</div>
			</div>
		</div>
	);
}
