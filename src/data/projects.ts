import { Parser, Store } from "n3";
import { readFileSync } from "fs";
import { join } from "path";

export interface Project {
  name: string;
  repo?: string;
  website?: string;
  description: string;
  categories: string[];
}

export interface ProjectsData {
  projects: Project[];
}

// Helper function to extract literal value from RDF term
const getLiteralValue = (
  store: Store,
  subject: string,
  predicate: string
): string | undefined => {
  const terms = store.getObjects(subject, predicate, null);
  return terms.length > 0 ? terms[0].value : undefined;
};

// Helper function to get all values for a predicate
const getAllValues = (
  store: Store,
  subject: string,
  predicate: string
): string[] => {
  return store.getObjects(subject, predicate, null).map((term) => term.value);
};

const loadProjectsFromTurtle = async (): Promise<ProjectsData> => {
  const ttlPath = join(process.cwd(), "src/data/projects.ttl");
  const ttlContent = readFileSync(ttlPath, "utf-8");

  const store = new Store();
  const parser = new Parser();

  return new Promise<ProjectsData>((resolve) => {
    parser.parse(ttlContent, (_error, quad, _prefixes) => {
      if (quad) {
        store.add(quad);
      } else {
        const projects = store
          .getSubjects(null, "http://usefulinc.com/ns/doap#Project", null)
          .map((subject) => {
            const subjectStr = subject.value;
            return {
              name:
                getLiteralValue(
                  store,
                  subjectStr,
                  "http://purl.org/dc/terms/title"
                ) || "",
              repo: getLiteralValue(
                store,
                subjectStr,
                "http://usefulinc.com/ns/doap#repository"
              ),
              website: getLiteralValue(
                store,
                subjectStr,
                "http://schema.org/url"
              ),
              description:
                getLiteralValue(
                  store,
                  subjectStr,
                  "http://purl.org/dc/terms/description"
                ) || "",
              categories: getAllValues(
                store,
                subjectStr,
                "http://osteele.com/ns/category"
              ),
            };
          });

        resolve({ projects });
      }
    });
  });
};

// Since we're in a module context, we can use top-level await
export const projectsData = await loadProjectsFromTurtle();

export default projectsData;
