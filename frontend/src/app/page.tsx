import { Suspense } from "react";
import { ContactSection } from "@/components/sections/ContactSection";
import {
  AcademySectionLoader,
  PortfolioSectionLoader,
} from "@/components/sections/loaders/DataSectionLoaders";
import {
  HeroSectionLoader,
  SiteContentSectionsLoader,
} from "@/components/sections/loaders/SiteContentLoaders";
import {
  AcademySectionSkeleton,
  ContactSectionSkeleton,
  HeroSectionSkeleton,
  PortfolioSectionSkeleton,
  SiteContentSkeleton,
} from "@/components/skeleton/SectionSkeletons";

/**
 * Page d'accueil SDEV — sections streamées avec skeletons Suspense.
 */
export default function HomePage() {
  return (
    <>
      <Suspense fallback={<HeroSectionSkeleton />}>
        <HeroSectionLoader />
      </Suspense>

      <Suspense fallback={<SiteContentSkeleton />}>
        <SiteContentSectionsLoader />
      </Suspense>

      <Suspense fallback={<PortfolioSectionSkeleton />}>
        <PortfolioSectionLoader />
      </Suspense>

      <Suspense fallback={<AcademySectionSkeleton />}>
        <AcademySectionLoader />
      </Suspense>

      <Suspense fallback={<ContactSectionSkeleton />}>
        <ContactSection />
      </Suspense>
    </>
  );
}
