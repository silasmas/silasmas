import type { NextConfig } from "next";
import path from "path";
import { fileURLToPath } from "url";

const frontendRoot = path.dirname(fileURLToPath(import.meta.url));
const apiUrl = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";
const apiOrigin = new URL(apiUrl.replace(/\/api\/?$/, "/"));

const nextConfig: NextConfig = {
  turbopack: {
    root: frontendRoot,
  },
  async headers() {
    return [
      {
        source: "/:path*",
        headers: [
          {
            key: "Vary",
            value:
              "RSC, Next-Router-State-Tree, Next-Router-Prefetch, Next-Url, Accept-Encoding",
          },
        ],
      },
    ];
  },
  images: {
    remotePatterns: [
      {
        protocol: apiOrigin.protocol.replace(":", "") as "http" | "https",
        hostname: apiOrigin.hostname,
        pathname: "/storage/**",
      },
      {
        protocol: apiOrigin.protocol.replace(":", "") as "http" | "https",
        hostname: apiOrigin.hostname,
        pathname: "/assets/**",
      },
      {
        protocol: "http",
        hostname: "localhost",
        pathname: "/storage/**",
      },
      {
        protocol: "http",
        hostname: "127.0.0.1",
        pathname: "/storage/**",
      },
      { protocol: "https", hostname: "images.unsplash.com" },
      { protocol: "https", hostname: "images.pexels.com" },
    ],
  },
};

export default nextConfig;
