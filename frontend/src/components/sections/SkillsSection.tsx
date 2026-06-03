import { SKILLS } from "@/data/site";

/**
 * Section compétences avec barres de progression.
 */
export function SkillsSection() {
  return (
    <section id="skills" className="section section-dark">
      <div className="container">
        <div className="mb-12 text-center">
          <span className="section-eyebrow">Expertise</span>
          <h2 className="section-title">Compétences</h2>
        </div>

        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {SKILLS.map((skill) => (
            <div key={skill.name} className="glass rounded-2xl p-5">
              <div className="mb-3 flex items-center justify-between text-sm">
                <span className="font-medium">{skill.name}</span>
                <span className="text-amber-400">{skill.value}%</span>
              </div>
              <div className="h-2 overflow-hidden rounded-full bg-white/10">
                <div
                  className="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500"
                  style={{ width: `${skill.value}%` }}
                />
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
