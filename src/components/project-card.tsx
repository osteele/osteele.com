import Link from "next/link";
import { FaExternalLinkAlt, FaGithub } from "react-icons/fa";
import type { Project } from "@/data/projects";

export const ProjectCard = ({ project }: { project: Project }) => (
  <div className="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 shadow-md hover:shadow-lg transition-all">
    <div className="absolute inset-0 bg-gradient-to-br from-white from-40% via-[#FF6B4A]/5 via-60% to-[#FF6B4A]/10 dark:from-gray-800 dark:via-[#FF8A6B]/5 dark:to-[#FF8A6B]/20" />
    <div className="relative p-4">
      <div className="card-body p-0">
        <h3 className="card-title text-xl mb-2">{project.name}</h3>

        <p className="text-gray-600 dark:text-gray-300 mb-3">
          {project.description}
        </p>

        <div className="flex flex-wrap gap-3">
          {project.website && (
            <Link
              href={project.website}
              className="flex items-center gap-2 text-[#FF6B4A] dark:text-[#FF8A6B] hover:underline"
            >
              <FaExternalLinkAlt className="h-4 w-4" />
              <span>Website</span>
            </Link>
          )}
          {project.repo && (
            <Link
              href={project.repo}
              className="flex items-center gap-2 text-[#FF6B4A] dark:text-[#FF8A6B] hover:underline"
            >
              <FaGithub className="h-4 w-4" />
              <span>Repository</span>
            </Link>
          )}
        </div>
      </div>
    </div>
  </div>
);
