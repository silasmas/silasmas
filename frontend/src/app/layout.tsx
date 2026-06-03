import type { Metadata } from "next";
import { Inter, Plus_Jakarta_Sans } from "next/font/google";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import "./globals.css";

const displayFont = Plus_Jakarta_Sans({
  variable: "--font-display",
  subsets: ["latin"],
  weight: ["500", "600", "700", "800"],
});

const bodyFont = Inter({
  variable: "--font-body",
  subsets: ["latin"],
  weight: ["400", "500", "600", "700"],
});

export const metadata: Metadata = {
  title: "Silas Développe | Solutions numériques & SDev Academy",
  description:
    "SDEV — solutions informatiques, développement web et mobile, marketing digital. Découvrez SDev Academy, notre branche formation.",
  keywords: [
    "sdev",
    "silasmas",
    "développement web",
    "mobile",
    "Kinshasa",
    "SDev Academy",
  ],
  openGraph: {
    title: "Silas Développe",
    description: "Expertise en programmation, développement web et mobile.",
    images: ["/images/logo.png"],
  },
  metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000"),
};

/**
 * Layout racine du site public Next.js.
 */
export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="fr" className={`${displayFont.variable} ${bodyFont.variable}`}>
      <body>
        <Header />
        <main>{children}</main>
        <Footer />
      </body>
    </html>
  );
}
