import { Component, type ReactNode } from "react";

interface Props {
	children: ReactNode;
	fallback?: ReactNode;
}

interface State {
	hasError: boolean;
	error?: Error;
}

export class ErrorBoundary extends Component<Props, State> {
	constructor(props: Props) {
		super(props);
		this.state = { hasError: false };
	}

	static getDerivedStateFromError(error: Error): State {
		return { hasError: true, error };
	}

	componentDidCatch(error: Error, errorInfo: React.ErrorInfo): void {
		console.error("ErrorBoundary caught an error:", error, errorInfo);
	}

	render(): ReactNode {
		if (this.state.hasError) {
			if (this.props.fallback) {
				return this.props.fallback;
			}

			return (
				<div className="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">
					<div className="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
						<h2 className="text-2xl font-bold text-red-600 dark:text-red-400 mb-4">
							Something went wrong
						</h2>
						<p className="text-gray-700 dark:text-gray-300 mb-4">
							We're sorry, but something unexpected happened. Please try refreshing the page.
						</p>
						{this.state.error && (
							<details className="mt-4">
								<summary className="cursor-pointer text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
									Error details
								</summary>
								<pre className="mt-2 p-4 bg-gray-100 dark:bg-gray-900 rounded text-xs overflow-auto">
									{this.state.error.message}
								</pre>
							</details>
						)}
						<button
							type="button"
							onClick={() => window.location.reload()}
							className="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-colors"
						>
							Reload page
						</button>
					</div>
				</div>
			);
		}

		return this.props.children;
	}
}
