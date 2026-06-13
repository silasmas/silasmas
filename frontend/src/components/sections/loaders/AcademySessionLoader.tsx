import { notFound, redirect } from "next/navigation";
import { AcademySessionRegistration } from "@/components/academy/AcademySessionRegistration";
import { PreRegistrationView } from "@/components/academy/PreRegistrationView";
import { ScrollToFormFab } from "@/components/academy/ScrollToFormFab";
import { SessionPoster } from "@/components/academy/SessionPoster";
import { RichHtmlReadMore } from "@/components/site/RichHtmlReadMore";
import { Container } from "@/components/site/Container";
import { Eyebrow } from "@/components/site/Eyebrow";
import { getOpenSessions, getSessionBySlug } from "@/lib/api";
import { isAcademyLaunchMode, resolvePrimarySessionSlug } from "@/lib/launch";

interface AcademySessionLoaderProps {
  slug: string;
}

/**
 * Charge et affiche le détail d'une session Academy (inscription + paiement).
 */
export async function AcademySessionLoader({ slug }: AcademySessionLoaderProps) {
  const session = await getSessionBySlug(slug);

  if (!session) {
    const openSessions = await getOpenSessions();
    const primarySlug = resolvePrimarySessionSlug(openSessions);

    if (primarySlug && primarySlug !== slug) {
      redirect(`/academy/${primarySlug}`);
    }

    notFound();
  }

  const launchMode = isAcademyLaunchMode();

  if (session.shows_pre_registration_page) {
    return <PreRegistrationView session={session} />;
  }

  return (
    <section className="py-10 md:py-16 lg:py-20">
      <Container size="wide">
        <div className="mb-6 md:mb-8">
          <Eyebrow>SDev Academy — Formation en ligne</Eyebrow>
          <h1 className="font-display mt-4 max-w-4xl text-3xl leading-[1.05] tracking-tight md:text-5xl lg:text-[3.25rem]">
            {session.title}
          </h1>
          {session.subtitle && (
            <p className="mt-4 max-w-3xl text-lg text-muted md:text-xl">{session.subtitle}</p>
          )}
        </div>

        <div className="grid gap-8 lg:grid-cols-[minmax(260px,380px)_minmax(0,1fr)] lg:items-start lg:gap-10 xl:gap-14">
          <aside className="lg:sticky lg:top-24">
            <SessionPoster
              session={session}
              priority
              variant="hero"
              className="w-full shadow-[0_24px_64px_rgba(0,31,63,0.18)]"
            />
          </aside>

          <div className="min-w-0">
            <AcademySessionRegistration session={session} variant="hero" />
          </div>
        </div>

        {!launchMode && (session.description || session.program) && (
          <div className="mt-14 grid gap-8 lg:grid-cols-2">
            {session.description && (
              <div className="card-lg p-6 md:p-7">
                <h2 className="font-display mb-3 text-2xl tracking-tight">Description</h2>
                <RichHtmlReadMore
                  html={session.description}
                  className="text-muted"
                  variant="card"
                />
              </div>
            )}
            {session.program && (
              <div className="card-lg p-6 md:p-7">
                <h2 className="font-display mb-3 text-2xl tracking-tight">Programme</h2>
                <RichHtmlReadMore
                  html={session.program}
                  className="text-sm text-muted"
                  variant="card"
                />
              </div>
            )}
          </div>
        )}
      </Container>

      <ScrollToFormFab />
    </section>
  );
}
