import { redirect } from "next/navigation";

interface AcademyResumePageProps {
  params: Promise<{
    slug: string;
    token: string;
  }>;
}

/**
 * Route dédiée aux liens e-mail : redirige vers le formulaire avec reprise paiement.
 */
export default async function AcademyResumePage({ params }: AcademyResumePageProps) {
  const { slug, token } = await params;
  const safeToken = encodeURIComponent(token);

  redirect(`/academy/${slug}?reprendre=${safeToken}#inscription`);
}
