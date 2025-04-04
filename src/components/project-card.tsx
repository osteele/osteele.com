"use client";

import Link from "next/link";
import { FaExternalLinkAlt, FaGithub } from "react-icons/fa";
import type { Project } from "@/data/projects";
import { useRef, useEffect, useState } from "react";

export const ProjectCard = ({ project }: { project: Project }) => {
  const formattedDate = project.dateCreated
    ? new Date(project.dateCreated).getFullYear()
    : undefined;

  const descriptionRef = useRef<HTMLParagraphElement>(null);
  const [isTruncated, setIsTruncated] = useState(false);

  useEffect(() => {
    const checkTruncation = () => {
      const element = descriptionRef.current;
      if (element) {
        setIsTruncated(
          element.scrollHeight > element.clientHeight ||
            element.scrollWidth > element.clientWidth
        );
      }
    };

    checkTruncation();
    window.addEventListener("resize", checkTruncation);

    return () => {
      window.removeEventListener("resize", checkTruncation);
    };
  }, [project.description]);

  return (
    <div className="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all flex flex-col h-full">
      <div className="absolute inset-0 bg-gradient-to-br from-white from-40% via-[#FF6B4A]/5 via-60% to-[#FF6B4A]/10 dark:from-gray-800 dark:via-[#FF8A6B]/5 dark:to-[#FF8A6B]/20" />
      <div className="relative p-5 flex-grow flex flex-col">
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

        <div className="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600 dark:text-gray-400">
          {project.primaryLanguage && (
            <div className="flex items-center gap-1.5">
              <span className="text-gray-500 dark:text-gray-500">
                Language:
              </span>
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

        <div className="mt-3 relative group/tooltip">
          <div className="flex items-start gap-1">
            <p
              ref={descriptionRef}
              className="text-gray-600 dark:text-gray-300 line-clamp-2 text-sm cursor-help flex-grow"
            >
              {project.description}
            </p>
            {isTruncated && (
              <span className="text-xs text-gray-400 dark:text-gray-500 mt-1 group-hover/tooltip:text-[#FF6B4A] dark:group-hover/tooltip:text-[#FF8A6B] transition-colors">
                ⓘ
              </span>
            )}
          </div>
          <div className="invisible opacity-0 group-hover/tooltip:visible group-hover/tooltip:opacity-100 transition-all duration-300 absolute left-0 bottom-full mb-1 z-50 w-60 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 text-sm">
            <div className="max-h-64 overflow-y-auto">
              {project.description}
            </div>
          </div>
        </div>

        <div className="flex-grow"></div>

        <div className="mt-4 flex gap-4">
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
