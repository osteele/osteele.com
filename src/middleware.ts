import { defineMiddleware } from "astro:middleware";

export const onRequest = defineMiddleware(async (context, next) => {
	const response = await next();

	// Add Content-Security-Policy header
	// Note: Adjust these directives based on your specific needs
	const csp = [
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline' 'unsafe-eval'", // unsafe-inline/eval needed for Astro's client-side scripts
		"style-src 'self' 'unsafe-inline'", // unsafe-inline needed for Tailwind and inline styles
		"img-src 'self' data: https:",
		"font-src 'self' data:",
		"connect-src 'self'",
		"frame-ancestors 'none'",
		"base-uri 'self'",
		"form-action 'self'",
	].join("; ");

	response.headers.set("Content-Security-Policy", csp);

	// Add other security headers
	response.headers.set("X-Frame-Options", "DENY");
	response.headers.set("X-Content-Type-Options", "nosniff");
	response.headers.set("Referrer-Policy", "strict-origin-when-cross-origin");
	response.headers.set("Permissions-Policy", "geolocation=(), microphone=(), camera=()");

	return response;
});
