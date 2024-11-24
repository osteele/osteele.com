import Link from "next/link";
import { FaExternalLinkAlt, FaGithub } from "react-icons/fa";
import type { Project } from "@/data/projects";

export const ProjectCard = ({ project }: { project: Project }) => (
  <div className="card bg-base-100 shadow-md hover:shadow-lg transition-shadow p-4 rounded-lg border border-gray-200 dark:border-gray-700">
    <div className="card-body p-0">
      <h3 className="card-title text-xl mb-2">{project.name}</h3>

      <p className="text-gray-600 dark:text-gray-300 mb-3">
        {project.description}
      </p>

      <div className="flex flex-wrap gap-3">
        {project.website && (
          <Link
            href={project.website}
            className="flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline"
          >
            <FaExternalLinkAlt className="h-4 w-4" />
            <span>Website</span>
          </Link>
        )}
        {project.repo && (
          <Link
            href={project.repo}
            className="flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline"
          >
            <FaGithub className="h-4 w-4" />
            <span>Repository</span>
          </Link>
        )}
      </div>
    </div>
  </div>
);
