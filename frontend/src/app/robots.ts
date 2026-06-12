import type { MetadataRoute } from "next";
import { siteBaseUrl } from "@/lib/seo";

/**
 * Politique robots.txt pour le référencement.
 */
export default function robots(): MetadataRoute.Robots {
  const base = siteBaseUrl();

  return {
    rules: [
      {
        userAgent: "*",
        allow: "/",
        disallow: ["/academy/espace/"],
      },
    ],
    sitemap: `${base}/sitemap.xml`,
  };
}
