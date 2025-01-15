import { Parser, Store } from "n3";
import { readFileSync } from "fs";
import { join } from "path";

export interface Project {
  name: string;
  repo?: string;
  website?: string;
  description: string;
  categories: string[];
  primaryLanguage?: string;
  dateCreated?: string;
  dateModified?: string;
  isArchived?: boolean;
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

  // Define the namespaces
  const DC = "http://purl.org/dc/terms/";
  const DOAP = "http://usefulinc.com/ns/doap#";
  const SCHEMA = "http://schema.org/";
  const OS = "http://osteele.com/ns/";

  await new Promise<void>((resolve, reject) => {
    parser.parse(ttlContent, (error, quad) => {
      if (error) reject(error);
      if (quad) store.add(quad);
      else resolve();
    });
  });

  const subjects = store.getSubjects(null, null, null);
  const projects = subjects
    .filter((subject) => {
      const includeInPortfolio = getLiteralValue(store, subject.value, OS + "includeInPortfolio");
      return includeInPortfolio === undefined || includeInPortfolio === "true";
    })
    .map((subject) => {
      const subjectStr = subject.value;
      const name = getLiteralValue(store, subjectStr, DC + "title") || "";
      const repo = getLiteralValue(store, subjectStr, DOAP + "repository");
      const website = getLiteralValue(store, subjectStr, SCHEMA + "url");
      const description = getLiteralValue(store, subjectStr, DC + "description") || "";
      const categories = getAllValues(store, subjectStr, OS + "category");
      const primaryLanguage = getLiteralValue(store, subjectStr, OS + "primaryLanguage");
      const dateCreated = getLiteralValue(store, subjectStr, SCHEMA + "dateCreated");
      const dateModified = getLiteralValue(store, subjectStr, SCHEMA + "dateModified");
      const isArchived = getLiteralValue(store, subjectStr, OS + "isArchived") === "true";

      return {
        name,
        repo,
        website,
        description,
        categories,
        primaryLanguage,
        dateCreated,
        dateModified,
        isArchived,
      };
    });

  return { projects };
};

// Since we're in a module context, we can use top-level await
export const projectsData = await loadProjectsFromTurtle();

export default projectsData;
