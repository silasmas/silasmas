import {
  SkeletonBlock,
  SkeletonLine,
  SkeletonSection,
  SkeletonSectionHeader,
} from "@/components/skeleton/Skeleton";

/**
 * Skeleton de la section hero.
 */
export function HeroSectionSkeleton() {
  return (
    <SkeletonSection label="Chargement de la section d'accueil">
      <section className="section">
        <div className="container grid min-h-[70vh] items-center gap-12 py-16 lg:grid-cols-2">
          <div className="space-y-6">
            <SkeletonLine className="h-6 w-40" />
            <SkeletonLine className="h-12 w-full max-w-lg" />
            <SkeletonLine className="h-12 w-4/5 max-w-md" />
            <SkeletonLine className="h-5 w-72 max-w-full" />
            <div className="flex gap-4 pt-2">
              <SkeletonBlock className="h-12 w-40 rounded-full" />
              <SkeletonBlock className="h-12 w-36 rounded-full" />
            </div>
          </div>
          <SkeletonBlock className="hidden min-h-[320px] rounded-3xl lg:block" />
        </div>
      </section>
    </SkeletonSection>
  );
}

/**
 * Skeleton de la section à propos.
 */
export function AboutSectionSkeleton() {
  return (
    <SkeletonSection label="Chargement de la section à propos">
      <section className="section">
        <div className="container grid items-center gap-12 lg:grid-cols-[320px_1fr]">
          <SkeletonBlock className="mx-auto aspect-square w-full max-w-xs rounded-[2rem]" />
          <div className="space-y-4">
            <SkeletonLine className="h-6 w-28" />
            <SkeletonLine className="h-10 w-full max-w-xl" />
            <SkeletonLine className="h-4 w-full" />
            <SkeletonLine className="h-4 w-full" />
            <SkeletonLine className="h-4 w-4/5" />
          </div>
        </div>
      </section>
    </SkeletonSection>
  );
}

/**
 * Skeleton de la section compétences.
 */
export function SkillsSectionSkeleton() {
  return (
    <SkeletonSection label="Chargement des compétences">
      <section className="section section-dark">
        <div className="container">
          <SkeletonSectionHeader centered />
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, index) => (
              <SkeletonBlock key={index} className="h-24 rounded-2xl" />
            ))}
          </div>
        </div>
      </section>
    </SkeletonSection>
  );
}

/**
 * Skeleton de la section services.
 */
export function ServicesSectionSkeleton() {
  return (
    <SkeletonSection label="Chargement des services">
      <section className="section">
        <div className="container">
          <SkeletonSectionHeader centered />
          <div className="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            {Array.from({ length: 4 }).map((_, index) => (
              <SkeletonBlock key={index} className="h-48 rounded-3xl" />
            ))}
          </div>
        </div>
      </section>
    </SkeletonSection>
  );
}

/**
 * Skeleton about + skills + services combinés.
 */
export function SiteContentSkeleton() {
  return (
    <>
      <AboutSectionSkeleton />
      <SkillsSectionSkeleton />
      <ServicesSectionSkeleton />
    </>
  );
}

/**
 * Skeleton de la section portfolio.
 */
export function PortfolioSectionSkeleton() {
  return (
    <SkeletonSection label="Chargement du portfolio">
      <section className="section section-dark">
        <div className="container">
          <SkeletonSectionHeader centered />
          <div className="mb-10 flex justify-center gap-3">
            {[1, 2, 3].map((item) => (
              <SkeletonBlock key={item} className="h-10 w-24 rounded-full" />
            ))}
          </div>
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, index) => (
              <SkeletonBlock key={index} className="aspect-[4/3] rounded-3xl" />
            ))}
          </div>
        </div>
      </section>
    </SkeletonSection>
  );
}

/**
 * Skeleton de la section Academy.
 */
export function AcademySectionSkeleton() {
  return (
    <SkeletonSection label="Chargement de SDev Academy">
      <section className="section">
        <div className="container">
          <SkeletonSectionHeader centered />
          <SkeletonBlock className="h-[420px] rounded-[2rem]" />
        </div>
      </section>
    </SkeletonSection>
  );
}

/**
 * Skeleton de la section contact.
 */
export function ContactSectionSkeleton() {
  return (
    <SkeletonSection label="Chargement du contact">
      <section className="section section-dark">
        <div className="container">
          <SkeletonSectionHeader centered />
          <div className="grid gap-10 lg:grid-cols-[1fr_1.4fr]">
            <div className="space-y-6">
              {[1, 2, 3].map((item) => (
                <SkeletonBlock key={item} className="h-24 rounded-2xl" />
              ))}
            </div>
            <SkeletonBlock className="min-h-[420px] rounded-3xl" />
          </div>
        </div>
      </section>
    </SkeletonSection>
  );
}

/**
 * Skeleton complet de la page d'accueil.
 */
export function HomePageSkeleton() {
  return (
    <>
      <HeroSectionSkeleton />
      <SiteContentSkeleton />
      <PortfolioSectionSkeleton />
      <AcademySectionSkeleton />
      <ContactSectionSkeleton />
    </>
  );
}

/**
 * Skeleton de la page détail session Academy.
 */
export function AcademySessionPageSkeleton() {
  return (
    <SkeletonSection label="Chargement de la session">
      <section className="section">
        <div className="container max-w-5xl">
          <div className="mb-10 grid gap-8 lg:grid-cols-[1fr_320px]">
            <div className="space-y-4">
              <SkeletonLine className="h-6 w-32" />
              <SkeletonLine className="h-10 w-full max-w-xl" />
              <SkeletonLine className="h-5 w-64" />
              <SkeletonLine className="h-5 w-80 max-w-full" />
            </div>
            <SkeletonBlock className="aspect-[3/4] min-h-[280px] rounded-2xl" />
          </div>
          <div className="grid gap-10 lg:grid-cols-2">
            <div className="space-y-6">
              <SkeletonBlock className="h-40 rounded-3xl" />
              <SkeletonBlock className="h-56 rounded-3xl" />
            </div>
            <SkeletonBlock className="min-h-[480px] rounded-3xl" />
          </div>
        </div>
      </section>
    </SkeletonSection>
  );
}
