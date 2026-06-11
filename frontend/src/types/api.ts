/**
 * Types partagés pour les réponses API Laravel.
 */

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface ProjectMetric {
  label: string;
  value: string;
}

export interface Project {
  id: number;
  project_name: string;
  slug?: string | null;
  project_description?: string;
  client_name?: string | null;
  category?: string | null;
  project_date?: string | null;
  context?: string | null;
  challenge?: string | null;
  outcome?: string | null;
  tags?: string[];
  metrics?: ProjectMetric[];
  web_url?: string | null;
  android_url?: string | null;
  ios_url?: string | null;
  logo_url?: string | null;
  gallery_urls?: string[];
  sort_order?: number;
}

export interface TrainingSession {
  id: number;
  title: string;
  slug: string;
  subtitle?: string | null;
  description?: string | null;
  program?: string | null;
  start_date: string;
  end_date: string;
  format: string;
  status: string;
  is_featured: boolean;
  accepts_registrations: boolean;
  cover_image?: string | null;
  cover_image_url?: string | null;
  spot_video_type?: "none" | "file" | "youtube" | "vimeo";
  spot_video_url?: string | null;
  spot_video_embed_url?: string | null;
  spot_video_watch_url?: string | null;
  spot_video_thumbnail_url?: string | null;
  share_url?: string | null;
  is_free?: boolean;
  is_paid?: boolean;
  price?: number | null;
  currency?: string;
  formatted_price?: string | null;
  price_usd?: number | null;
  price_cdf?: number | null;
  exchange_rate_usd_cdf?: number | null;
  payment_currency_options?: PaymentCurrencyOption[];
  notify_by_email?: boolean;
  notify_by_sms?: boolean;
  notify_by_whatsapp?: boolean;
  payment_mobile_money_enabled?: boolean;
  payment_card_enabled?: boolean;
  enabled_mobile_operators?: MobileMoneyOperator[];
  confidentiality_notice?: string | null;
  participant_benefits?: string | null;
  registration_benefits?: string[];
  session_resources?: SessionResourceItem[];
}

export interface SessionResourceItem {
  title: string;
  url: string;
  description?: string | null;
}

export type MobileMoneyOperator = "mpesa" | "airtel" | "orange" | "afrimoney";

export type PaymentChannel = "mobile_money" | "card";

export type PaymentCurrency = "USD" | "CDF" | "EUR";

export interface PaymentCurrencyOption {
  currency: PaymentCurrency;
  amount: number;
  formatted?: string;
}

export interface SessionPaymentInfo {
  reference: string;
  amount: number;
  currency: string;
  status: string;
  currency_options?: PaymentCurrencyOption[];
  equivalent_pricing?: boolean;
}

export type RegistrationResumeAction = "payment" | "participant_space";

export interface RegistrationResult {
  registration: {
    id: number;
    status: string;
    requires_payment?: boolean;
    access_token?: string;
    participant_url?: string;
    student: { firstname: string; lastname: string; email: string };
    training_session: { title: string; slug: string };
  };
  requires_payment: boolean;
  payment: SessionPaymentInfo | null;
  access_token?: string;
  participant_url?: string;
  resume_action?: RegistrationResumeAction;
  already_registered?: boolean;
  is_paid?: boolean;
}

export interface ProcessPaymentPayload {
  reference: string;
  channel: PaymentChannel;
  payment_currency?: PaymentCurrency;
  phone?: string;
  mobile_operator?: MobileMoneyOperator;
}

export interface ProcessPaymentResponse {
  reponse: boolean;
  message?: string;
  type?: "mobile" | "card" | "already_paid";
  status?: number;
  redirect_url?: string;
  orderNumber?: string;
}

export interface CheckPaymentStatusResponse {
  reponse: boolean;
  status?: number;
  confirmed?: boolean;
  message?: string;
  registration_status?: string;
}

export interface SiteHero {
  eyebrow?: string | null;
  headline?: string | null;
  headline_accent?: string | null;
  body?: string | null;
  image?: string | null;
}

export interface SiteTestimonial {
  id: number;
  quote: string;
  author: string;
  role?: string | null;
}

export interface SitePrinciple {
  id: number;
  title: string;
  body: string;
}

export interface SiteFaq {
  id: number;
  q: string;
  a: string;
}

export interface SiteAbout {
  eyebrow?: string | null;
  title: string;
  body?: string | null;
  secondary_body?: string | null;
  image?: string | null;
}

export interface SiteSkill {
  id: number;
  name: string;
  value: number;
}

export interface SiteService {
  id: number;
  title: string;
  description?: string | null;
  excerpt?: string | null;
  icon: string;
}

export interface SilasJourneyStep {
  id: number;
  year: string;
  title: string;
  body: string;
}

export interface SilasOffer {
  id: number;
  icon: string;
  title: string;
  body: string;
}

export interface SilasPageContent {
  hero?: {
    eyebrow?: string | null;
    title?: string | null;
    accent?: string | null;
    body?: string | null;
    image?: string | null;
  } | null;
  availability?: {
    title?: string | null;
    body?: string | null;
  } | null;
  journey_intro?: {
    title?: string | null;
    body?: string | null;
  } | null;
  journey?: SilasJourneyStep[];
  banner?: {
    badge?: string | null;
    title?: string | null;
    image?: string | null;
  } | null;
  offers?: SilasOffer[];
  cta?: {
    title?: string | null;
    subtitle?: string | null;
    cta?: string | null;
  } | null;
}

export interface SiteContent {
  hero?: SiteHero | null;
  about: SiteAbout | null;
  skills: SiteSkill[];
  services: SiteService[];
  testimonials?: SiteTestimonial[];
  principles?: SitePrinciple[];
  faqs?: SiteFaq[];
  client_logos?: string[];
  hero_taglines: string[];
  silas_page?: SilasPageContent | null;
  settings?: SiteSettings;
}

export interface SiteSettings {
  site_title: string;
  site_tagline?: string | null;
  logo_url?: string | null;
  favicon_url?: string | null;
  email?: string | null;
  phone_primary?: string | null;
  phone_secondary?: string | null;
  address?: string | null;
  footer_description?: string | null;
  usd_to_cdf_rate?: number | null;
}

export interface RegistrationPayload {
  training_session_slug: string;
  firstname: string;
  lastname: string;
  email: string;
  phone?: string;
  city?: string;
  country?: string;
  education_level?: string;
  motivation?: string;
  marketing_opt_in?: boolean;
  notify_email?: boolean;
  notify_sms?: boolean;
  notify_whatsapp?: boolean;
}

export interface ParticipantSpace {
  registration: {
    id: number;
    status: string;
    is_confirmed: boolean;
    confidentiality_accepted: boolean;
  };
  student: {
    firstname: string;
    lastname: string;
    email: string;
    phone?: string | null;
    city?: string | null;
    country?: string | null;
    education_level?: string | null;
  };
  session: {
    title: string;
    slug: string;
    subtitle?: string | null;
    start_date: string;
    end_date: string;
    format: string;
    participant_benefits?: string | null;
    confidentiality_notice?: string | null;
    resources: SessionResourceItem[];
  };
  participant_url: string;
  countdown_target: string;
}

export interface ContactPayload {
  nom: string;
  email: string;
  phone: string;
  subject: string;
  message: string;
}
