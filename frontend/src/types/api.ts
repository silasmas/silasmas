/**
 * Types partagés pour les réponses API Laravel.
 */

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface Project {
  id: number;
  project_name: string;
  project_description?: string;
  web_url?: string | null;
  android_url?: string | null;
  ios_url?: string | null;
  logo_url?: string | null;
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
}

export interface ContactPayload {
  nom: string;
  email: string;
  phone: string;
  subject: string;
  message: string;
}
