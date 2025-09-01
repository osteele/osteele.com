#!/usr/bin/env -S uv --quiet run --script
# /// script
# requires-python = ">=3.12"
# dependencies = [
#     "requests",
# ]
# ///

"""
Benchmark comparison between REST and GraphQL versions of update_projects.py
"""

import os
import time
import subprocess
import sys

def time_command(command, description):
    """Time a command execution."""
    print(f"\n{description}")
    print(f"Command: {command}")
    print("-" * 60)
    
    start = time.time()
    try:
        result = subprocess.run(
            command,
            shell=True,
            capture_output=True,
            text=True,
            timeout=300  # 5 minute timeout
        )
        elapsed = time.time() - start
        
        if result.returncode == 0:
            # Count the number of projects updated
            output = result.stdout + result.stderr
            if "projects would be updated" in output:
                print(output.split("\n")[-2])  # Print the summary line
            elif "No changes made" in output:
                print("No changes made.")
            print(f"✓ Completed in {elapsed:.2f} seconds")
        else:
            print(f"✗ Failed with error code {result.returncode}")
            if result.stderr:
                print(f"Error: {result.stderr[:200]}")
            print(f"Time before failure: {elapsed:.2f} seconds")
    except subprocess.TimeoutExpired:
        elapsed = time.time() - start
        print(f"✗ Timed out after {elapsed:.2f} seconds")
    except Exception as e:
        elapsed = time.time() - start
        print(f"✗ Error: {e}")
        print(f"Time before error: {elapsed:.2f} seconds")
    
    return elapsed

def main():
    if not os.environ.get("GITHUB_TOKEN"):
        print("Error: GITHUB_TOKEN environment variable is required")
        sys.exit(1)
    
    print("=" * 60)
    print("UPDATE PROJECTS BENCHMARK COMPARISON")
    print("=" * 60)
    
    # Test with a small subset first
    print("\n### Testing with 3 specific projects ###")
    
    rest_time = time_command(
        'scripts/update_projects.py dates "Liquid Template Engine" "Gojekyll" "p5-server" --dry-run',
        "REST API Version (3 projects)"
    )
    
    graphql_time = time_command(
        'scripts/update_projects_graphql.py dates "Liquid Template Engine" "Gojekyll" "p5-server" --dry-run',
        "GraphQL Version (3 projects)"
    )
    
    if rest_time > 0 and graphql_time > 0:
        speedup = rest_time / graphql_time
        print(f"\n🚀 GraphQL is {speedup:.1f}x faster for 3 projects")
    
    # Test with all projects
    print("\n### Testing with ALL projects ###")
    
    rest_time_all = time_command(
        'scripts/update_projects.py dates --dry-run',
        "REST API Version (all projects)"
    )
    
    graphql_time_all = time_command(
        'scripts/update_projects_graphql.py dates --dry-run',
        "GraphQL Version (all projects)"
    )
    
    if rest_time_all > 0 and graphql_time_all > 0:
        speedup_all = rest_time_all / graphql_time_all
        print(f"\n🚀 GraphQL is {speedup_all:.1f}x faster for all projects")
        print(f"Time saved: {rest_time_all - graphql_time_all:.1f} seconds")

    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"REST API (3 projects): {rest_time:.2f}s")
    print(f"GraphQL (3 projects): {graphql_time:.2f}s")
    print(f"REST API (all projects): {rest_time_all:.2f}s")
    print(f"GraphQL (all projects): {graphql_time_all:.2f}s")
    
    # Note about the sleep delays
    print("\n📝 Note: The REST API version includes 1-second delays between")
    print("   each repository fetch to avoid rate limiting, which accounts")
    print("   for most of the performance difference.")

if __name__ == "__main__":
    main()