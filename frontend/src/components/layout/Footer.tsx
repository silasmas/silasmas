import Image from "next/image";
import Link from "next/link";

/**
 * Pied de page du site SDev.
 */
export function Footer() {
  return (
    <footer className="border-t border-amber-500/15 bg-[#0a0a0a] py-12">
      <div className="container grid gap-10 md:grid-cols-[1.2fr_1fr_1fr]">
        <div>
          <Image
            src="/images/logo.png"
            alt="Silas Développe"
            width={160}
            height={56}
            className="mb-4 h-12 w-auto object-contain"
          />
          <p className="max-w-md text-sm text-slate-400">
            SDEV offre des solutions informatiques, des accompagnements et conseils
            en stratégie marketing digitale et assure la couverture médiatique des
            évènements de tout genre.
          </p>
        </div>

        <div>
          <h3 className="mb-4 font-semibold text-white">Navigation</h3>
          <ul className="space-y-2 text-sm text-slate-400">
            <li><a href="#about" className="hover:text-amber-400">À propos</a></li>
            <li><a href="#services" className="hover:text-amber-400">Services</a></li>
            <li><a href="#portfolio" className="hover:text-amber-400">Portfolio</a></li>
            <li><Link href="/academy/programmation-assistee-ia-2026" className="hover:text-amber-400">SDev Academy</Link></li>
          </ul>
        </div>

        <div>
          <h3 className="mb-4 font-semibold text-white">Contact</h3>
          <ul className="space-y-2 text-sm text-slate-400">
            <li>ir-masimango@silasmas.com</li>
            <li>(+243) 827 839 232</li>
            <li>Kinshasa, RDC</li>
          </ul>
        </div>
      </div>

      <div className="container mt-10 border-t border-amber-500/10 pt-6 text-center text-sm text-slate-500">
        © {new Date().getFullYear()} Silas Développe (SDEV). Tous droits réservés.
      </div>
    </footer>
  );
}
