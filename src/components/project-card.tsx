import Link from "next/link";
import { FaExternalLinkAlt, FaGithub } from "react-icons/fa";
import type { Project } from "@/data/projects";

export const ProjectCard = ({ project }: { project: Project }) => {
  const formattedDate = project.dateCreated 
    ? new Date(project.dateCreated).getFullYear()
    : undefined;

  return (
    <div className="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all">
      <div className="absolute inset-0 bg-gradient-to-br from-white from-40% via-[#FF6B4A]/5 via-60% to-[#FF6B4A]/10 dark:from-gray-800 dark:via-[#FF8A6B]/5 dark:to-[#FF8A6B]/20" />
      <div className="relative p-5">
        <div className="flex justify-between items-start gap-4">
          <h3 className="text-xl font-semibold text-gray-900 dark:text-white">
            {project.name}
          </h3>
          {project.isArchived && (
            <span className="shrink-0 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
              Archived
            </span>
          )}
        </div>

        <div className="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
          {project.primaryLanguage && (
            <div className="flex items-center gap-1.5">
              <span className="text-gray-500 dark:text-gray-500">Language:</span>
              <span className="font-medium">{project.primaryLanguage}</span>
            </div>
          )}
          {formattedDate && (
            <div className="flex items-center gap-1.5">
              <span className="text-gray-500 dark:text-gray-500">Created:</span>
              <span className="font-medium">{formattedDate}</span>
            </div>
          )}
        </div>

        <p className="mt-3 text-gray-600 dark:text-gray-300 line-clamp-2">
          {project.description}
        </p>

        <div className="mt-4 flex flex-wrap gap-3">
          {project.website && (
            <Link
              href={project.website}
              className="inline-flex items-center gap-1.5 text-sm font-medium text-[#FF6B4A] dark:text-[#FF8A6B] hover:text-[#FF8A6B] dark:hover:text-[#FFA68B] transition-colors"
            >
              <FaExternalLinkAlt className="h-3.5 w-3.5" />
              <span>Website</span>
            </Link>
          )}
          {project.repo && (
            <Link
              href={project.repo}
              className="inline-flex items-center gap-1.5 text-sm font-medium text-[#FF6B4A] dark:text-[#FF8A6B] hover:text-[#FF8A6B] dark:hover:text-[#FFA68B] transition-colors"
            >
              <FaGithub className="h-3.5 w-3.5" />
              <span>Repository</span>
            </Link>
          )}
        </div>
      </div>
    </div>
  );
};
