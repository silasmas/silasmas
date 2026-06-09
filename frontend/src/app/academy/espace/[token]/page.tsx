import Link from "next/link";
import { ParticipantSpaceView } from "@/components/academy/ParticipantSpaceView";
import { Container } from "@/components/site/Container";

interface ParticipantPageProps {
  params: Promise<{ token: string }>;
}

/**
 * Page espace participant (compte à rebours, profil, ressources).
 */
export default async function ParticipantPage({ params }: ParticipantPageProps) {
  const { token } = await params;

  return (
    <section className="py-12 md:py-16">
      <Container className="max-w-3xl">
        <Link
          href="/"
          className="mb-8 inline-flex text-sm text-muted hover:text-academy"
        >
          ← Accueil
        </Link>
        <ParticipantSpaceView token={token} />
      </Container>
    </section>
  );
}
