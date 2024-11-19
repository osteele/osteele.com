import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  output: process.env.NODE_ENV === "production" ? "export" : undefined,
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "images.osteele.com",
        pathname: "/products/**",
      },
    ],
  },
};

export default nextConfig;
