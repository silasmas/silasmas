import { Suspense } from "react";
import { AcademySessionLoader } from "@/components/sections/loaders/AcademySessionLoader";
import { AcademySessionPageSkeleton } from "@/components/skeleton/SectionSkeletons";

interface AcademyPageProps {
  params: Promise<{ slug: string }>;
}

/**
 * Contenu async de la page session (params await).
 */
async function AcademySessionPageContent({ params }: AcademyPageProps) {
  const { slug } = await params;

  return <AcademySessionLoader slug={slug} />;
}

/**
 * Page détail session Academy avec skeleton Suspense.
 */
export default function AcademySessionPage({ params }: AcademyPageProps) {
  return (
    <Suspense fallback={<AcademySessionPageSkeleton />}>
      <AcademySessionPageContent params={params} />
    </Suspense>
  );
}
