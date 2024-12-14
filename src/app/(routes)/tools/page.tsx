import { PageLayout } from "@/components/page-layout";
import { getProjectsByCategory } from "@/lib/sections";
import { ToolsSections } from "@/data/sections";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ProjectCard } from "@/components/project-card";
import { Project } from "@/data/projects";
import { ResourceCard } from "@/components/resource-card";

// Helper function to categorize web apps
function categorizeWebApps(apps: Project[]) {
  const languageLearning = apps.filter(app => 
    app.categories.includes("language-learning")
  );
  const other = apps.filter(app => 
    !app.categories.includes("language-learning")
  );

  return {
    languageLearning,
    other
  };
}

// Helper function to categorize CLI tools
function categorizeCLITools(tools: Project[]) {
  const publishing = tools.filter(tool => 
    tool.categories.includes("web-publishing")
  );
  const classroom = tools.filter(tool => 
    tool.categories.includes("educator-tools")
  );
  const embroidery = tools.filter(tool => 
    tool.categories.includes("machine-embroidery")
  );
  const p5 = tools.filter(tool => 
    tool.categories.includes("p5js")
  );
  const other = tools.filter(tool => 
    !tool.categories.includes("web-publishing") && 
    !tool.categories.includes("educator-tools") &&
    !tool.categories.includes("machine-embroidery") &&
    !tool.categories.includes("p5js")
  );

  return {
    publishing,
    classroom,
    embroidery,
    p5,
    other
  };
}

function ToolSection({ title, tools }: { title: string; tools: Project[] }) {
  if (tools.length === 0) return null;
  
  return (
    <div className="mb-12 last:mb-0">
      <h3 className="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-200">
        {title}
      </h3>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {tools.map((tool) => (
          <ProjectCard key={tool.name} project={tool} />
        ))}
      </div>
    </div>
  );
}

export default function ToolsPage() {
  // Get all tools across all sections
  const allTools = ToolsSections.flatMap((section) => {
    const toolsData = getProjectsByCategory(section, "tools");
    return [
      ...toolsData.sectionProjects,
      ...Array.from(toolsData.subsectionProjects.values()).flat(),
    ];
  });

  const webApps = allTools.filter((tool) => tool.categories.includes("web-app"));
  const cliTools = allTools.filter((tool) =>
    tool.categories.includes("command-line-tool")
  );

  const webAppCategories = categorizeWebApps(webApps);
  const cliCategories = categorizeCLITools(cliTools);

  return (
    <PageLayout title="Tools">
      <div className="max-w-5xl mx-auto px-6 py-12">
        {/* Hero section with new typography */}
        <div className="mb-16 text-center">
          <h1 className="font-serif text-6xl md:text-7xl font-bold mb-6 tracking-tight text-gray-900 dark:text-gray-100">
            Tools
          </h1>
          <p className="text-xl text-[#FF6B4A] dark:text-[#FF8A6B] max-w-2xl mx-auto">
            Utilities for developers, language learners, and makers
          </p>
        </div>

        <Tabs defaultValue="web-apps" className="w-full">
          <TabsList className="grid w-full grid-cols-2 mb-8 bg-gray-100/50 dark:bg-gray-800/50">
            <TabsTrigger 
              value="web-apps"
              className="data-[state=active]:bg-white dark:data-[state=active]:bg-gray-700"
            >
              Web Apps
            </TabsTrigger>
            <TabsTrigger 
              value="command-line"
              className="data-[state=active]:bg-white dark:data-[state=active]:bg-gray-700"
            >
              Command Line
            </TabsTrigger>
          </TabsList>

          <TabsContent value="web-apps">
            <div className="max-w-5xl mx-auto">
              <h2 className="text-2xl font-semibold mb-8 text-[#FF6B4A] dark:text-[#FF8A6B]">
                Web Applications
              </h2>
              <ToolSection title="Language Learning" tools={webAppCategories.languageLearning} />
              <ToolSection title="Other Tools" tools={webAppCategories.other} />
              
              <div className="mt-16">
                <h2 className="text-2xl font-semibold mb-8 text-[#FF6B4A] dark:text-[#FF8A6B]">
                  Additional Web Apps
                </h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  <ResourceCard
                    title="Art Projects"
                    description="Interactive art and generative art projects"
                    href="https://osteele.notion.site/art"
                  />
                  <ResourceCard
                    title="Humor Projects"
                    description="Playful web applications and experiments"
                    href="https://osteele.notion.site/humor"
                  />
                  <ResourceCard
                    title="Educational Tools"
                    description="Interactive tools and visualizations for learning"
                    href="/teaching-materials#tools"
                  />
                </div>
              </div>
            </div>
          </TabsContent>

          <TabsContent value="command-line">
            <div className="max-w-5xl mx-auto">
              <h2 className="text-2xl font-semibold mb-8 text-[#FF6B4A] dark:text-[#FF8A6B]">
                Command Line Tools
              </h2>
              {cliCategories.publishing.length > 0 && (
                <ToolSection title="Web Publishing" tools={cliCategories.publishing} />
              )}
              {cliCategories.classroom.length > 0 && (
                <ToolSection title="Classroom Tools" tools={cliCategories.classroom} />
              )}
              {cliCategories.embroidery.length > 0 && (
                <ToolSection title="Machine Embroidery" tools={cliCategories.embroidery} />
              )}
              {cliCategories.p5.length > 0 && (
                <ToolSection title="P5.js Tools" tools={cliCategories.p5} />
              )}
              {cliCategories.other.length > 0 && (
                <ToolSection title="Other Tools" tools={cliCategories.other} />
              )}
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </PageLayout>
  );
}
