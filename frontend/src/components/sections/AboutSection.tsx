import Image from "next/image";

/**
 * Section à propos de SDEV.
 */
export function AboutSection() {
  return (
    <section id="about" className="section">
      <div className="container grid items-center gap-12 lg:grid-cols-[320px_1fr]">
        <div className="relative mx-auto w-full max-w-xs">
          <div className="absolute -inset-3 rounded-[2rem] bg-gradient-to-br from-amber-500/30 to-orange-600/10 blur-sm" />
          <div className="relative overflow-hidden rounded-[2rem] border border-amber-500/20 bg-black p-6">
            <Image
              src="/images/logo.png"
              alt="Logo Silas Développe"
              width={280}
              height={280}
              className="mx-auto h-auto w-full object-contain"
            />
          </div>
        </div>

        <div>
          <span className="section-eyebrow">À propos</span>
          <h2 className="section-title">
            Une vision numérique pour <span className="text-gold">l&apos;Afrique</span>
          </h2>
          <p className="mb-4 text-lg text-slate-300">
            SDEV est une société offrant des solutions informatiques, des accompagnements
            et conseils en stratégie marketing digitale et assure la couverture médiatique
            des évènements de tout genre.
          </p>
          <p className="text-slate-400">
            Avec <strong className="text-amber-400">SDev Academy</strong>, nous formons
            la prochaine génération de talents du numérique en RDC et sur le continent.
          </p>
        </div>
      </div>
    </section>
  );
}
